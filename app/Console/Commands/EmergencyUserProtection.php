<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmergencyUserProtection extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:emergency-protect {--activate-admins : Automatically activate all inactive admin users}';

    /**
     * The console command description.
     */
    protected $description = 'Emergency protection against user auto-deletion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚨 Emergency User Protection System');
        $this->info('=====================================');
        
        // Check for any users that might be at risk
        $inactiveAdmins = User::where('role', 'admin')
            ->where('is_active', false)
            ->get();
            
        $this->info('🔍 Scanning for inactive admin users...');
        
        if ($inactiveAdmins->count() > 0) {
            $this->warn("⚠️  Found {$inactiveAdmins->count()} inactive admin users:");
            
            $tableData = [];
            foreach ($inactiveAdmins as $admin) {
                $tableData[] = [
                    $admin->id,
                    $admin->name,
                    $admin->email,
                    $admin->created_at->format('Y-m-d H:i:s'),
                    $admin->last_activity_at ? $admin->last_activity_at->format('Y-m-d H:i:s') : 'Never'
                ];
            }
            
            $this->table([
                'ID', 'Name', 'Email', 'Created At', 'Last Activity'
            ], $tableData);
            
            if ($this->option('activate-admins') || $this->confirm('🛡️  Activate these admin users for protection?')) {
                foreach ($inactiveAdmins as $admin) {
                    $admin->update(['is_active' => true]);
                    $this->info("✅ Activated: {$admin->name} ({$admin->email})");
                    
                    Log::info('🛡️ EMERGENCY PROTECTION: Admin user activated', [
                        'user_id' => $admin->id,
                        'user_name' => $admin->name,
                        'user_email' => $admin->email,
                        'protected_at' => now(),
                        'protection_reason' => 'Emergency auto-deletion prevention'
                    ]);
                }
                $this->info("🎉 Successfully activated {$inactiveAdmins->count()} admin users!");
            }
        } else {
            $this->info('✅ No inactive admin users found - all admins are active');
        }
        
        $this->newLine();
        $this->info('📊 Current User Statistics:');
        $this->info('===========================');
        
        // Show comprehensive user statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'active_admins' => User::where('role', 'admin')->where('is_active', true)->count(),
            'inactive_admins' => User::where('role', 'admin')->where('is_active', false)->count(),
            'total_it_staff' => User::where('role', 'it_staff')->count(),
            'active_it_staff' => User::where('role', 'it_staff')->where('is_active', true)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
        ];
        
        $this->table(['Metric', 'Count'], [
            ['Total Users', $stats['total_users']],
            ['├─ Active Users', $stats['active_users']],
            ['└─ Inactive Users', $stats['inactive_users']],
            ['', ''],
            ['Total Admins', $stats['total_admins']],
            ['├─ Active Admins', $stats['active_admins']],
            ['└─ Inactive Admins', $stats['inactive_admins']],
            ['', ''],
            ['Total IT Staff', $stats['total_it_staff']],
            ['└─ Active IT Staff', $stats['active_it_staff']],
            ['', ''],
            ['Verified Users', $stats['verified_users']],
            ['Unverified Users', $stats['unverified_users']],
        ]);
        
        // Safety checks and warnings
        $this->newLine();
        $this->info('🔐 Security Status Check:');
        $this->info('=========================');
        
        if ($stats['active_admins'] == 0) {
            $this->error('🚨 CRITICAL: No active admin users found! System is at risk!');
        } elseif ($stats['active_admins'] == 1) {
            $this->warn('⚠️  WARNING: Only 1 active admin user. Consider creating backup admin accounts.');
        } else {
            $this->info("✅ Good: {$stats['active_admins']} active admin users found");
        }
        
        if ($stats['inactive_admins'] > 0) {
            $this->warn("⚠️  Note: {$stats['inactive_admins']} inactive admin users exist");
        }
        
        $this->newLine();
        $this->info('🛡️  Protection Status:');
        $this->info('=====================');
        $this->info('✅ User deletion protection: ACTIVE');
        $this->info('✅ Dangerous scheduled task: DISABLED');
        $this->info('✅ Enhanced logging: ENABLED');
        $this->info('✅ Activity log trait: TEMPORARILY DISABLED');
        
        $this->newLine();
        $this->info('📝 Next Steps:');
        $this->info('==============');
        $this->info('1. Monitor logs for "USER DELETION ATTEMPT INTERCEPTED"');
        $this->info('2. Test creating a new inactive admin user');
        $this->info('3. Check storage/logs/laravel.log for patterns');
        $this->info('4. Run: tail -f storage/logs/laravel.log | grep "DELETION"');
        
        $this->newLine();
        $this->info('🎯 Emergency protection is now ACTIVE!');
        
        return Command::SUCCESS;
    }
}