<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Expire old OTP requests every 5 minutes
        $schedule->command('otp:expire')->everyFiveMinutes();
        
        // Cleanup expired OTP sessions every hour
        $schedule->command('otp:expire --cleanup')->hourly();
        
        // Clean old logs weekly
        $schedule->command('logs:clean')->weekly();
        
        // 🚨 DISABLED: The dangerous user activity update that was causing issues
        // This scheduled task was marking users as inactive, which then triggered
        // some hidden logic that was deleting inactive admin users
        /*
        $schedule->call(function () {
            // Mark users as inactive if they haven't been active for 30 days
            \App\Models\User::where('last_activity_at', '<', now()->subDays(30))
                ->where('is_active', true)
                ->update(['is_active' => false]);
        })->daily();
        */
        
        // 🛡️ SAFER VERSION: Only log what would be affected, don't actually change anything
        $schedule->call(function () {
            $affectedUsers = \App\Models\User::where('last_activity_at', '<', now()->subDays(30))
                ->where('is_active', true)
                ->get();
                
            if ($affectedUsers->count() > 0) {
                Log::info('📊 SCHEDULED TASK: Users that would be marked inactive (SIMULATION ONLY)', [
                    'count' => $affectedUsers->count(),
                    'simulation_mode' => true,
                    'users' => $affectedUsers->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'last_activity' => $user->last_activity_at?->format('Y-m-d H:i:s')
                        ];
                    })
                ]);
            } else {
                Log::info('📊 SCHEDULED TASK: No users would be affected by inactivity check');
            }
            
            // 🚨 IMPORTANT: We're NOT actually updating anything anymore
            // This prevents the auto-deletion trigger
            // ->update(['is_active' => false]);
        })->daily();
        
        // Log OTP cleanup statistics daily at 2 AM
        $schedule->command('otp:expire --cleanup --stats')
            ->dailyAt('02:00')
            ->appendOutputTo(storage_path('logs/otp-cleanup.log'));

        // 🛡️ EMERGENCY: Add a daily check for orphaned inactive admin users
        $schedule->call(function () {
            $inactiveAdmins = \App\Models\User::where('role', 'admin')
                ->where('is_active', false)
                ->get();
                
            if ($inactiveAdmins->count() > 0) {
                Log::warning('🚨 ALERT: Found inactive admin users', [
                    'count' => $inactiveAdmins->count(),
                    'inactive_admins' => $inactiveAdmins->map(function($admin) {
                        return [
                            'id' => $admin->id,
                            'name' => $admin->name,
                            'email' => $admin->email,
                            'created_at' => $admin->created_at->format('Y-m-d H:i:s'),
                            'last_activity' => $admin->last_activity_at?->format('Y-m-d H:i:s') ?? 'Never'
                        ];
                    })
                ]);
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}