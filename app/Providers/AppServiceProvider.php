<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Credential;
use App\Models\User;
use App\Observers\CredentialObserver;
// use App\Observers\UserObserver; // PERMANENTLY DISABLED - causes user deletion issues

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register services here when you create them
    }

    public function boot(): void
    {
        // Use Bootstrap for pagination
        Paginator::useBootstrapFive();
        
        // Register observers
        Credential::observe(CredentialObserver::class);
        
        // FIX: UserObserver is PERMANENTLY DISABLED due to deletion issues
        // User::observe(UserObserver::class); // DO NOT UNCOMMENT - causes auto-deletion
        
        // Log that UserObserver is disabled
        Log::info('AppServiceProvider boot: UserObserver is disabled to prevent auto-deletion issues');

        // EMERGENCY: Block all user deletions at the model level
        User::deleting(function ($user) {
            Log::emergency('🚨 MODEL EVENT: User deletion intercepted', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'is_active' => $user->is_active,
                'timestamp' => now(),
                'stack_trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50)
            ]);

            // Check if this is coming from the admin controller
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
            $isAdminAction = false;
            
            foreach ($trace as $frame) {
                if (isset($frame['class']) && 
                    $frame['class'] === 'App\Http\Controllers\Admin\UserManagementController' &&
                    isset($frame['function']) && $frame['function'] === 'destroy') {
                    $isAdminAction = true;
                    break;
                }
            }
            
            if (!$isAdminAction) {
                Log::emergency('🛡️ BLOCKED: Non-admin user deletion', [
                    'user_id' => $user->id,
                    'blocked_at' => now(),
                    'trace_summary' => array_slice($trace, 0, 5)
                ]);
                
                // Prevent the deletion
                return false;
            }
            
            return true;
        });

        // Monitor all database queries for user deletions
        if (config('app.debug')) {
            \Illuminate\Support\Facades\DB::listen(function ($query) {
                if (stripos($query->sql, 'delete') !== false && stripos($query->sql, 'users') !== false) {
                    Log::emergency('🗄️ SQL DELETION QUERY DETECTED', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                        'timestamp' => now(),
                        'stack_trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 30)
                    ]);
                }
            });
        }
        
        // Custom validation rules
        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            if (!class_exists('\App\Helpers\SecurityHelper')) {
                // Fallback validation if SecurityHelper doesn't exist
                return strlen($value) >= 8 && 
                       preg_match('/[a-z]/', $value) && 
                       preg_match('/[A-Z]/', $value) && 
                       preg_match('/[0-9]/', $value);
            }
            return \App\Helpers\SecurityHelper::calculatePasswordStrength($value) >= 3;
        });

        // Add model event logging for debugging
        User::creating(function ($user) {
            Log::info('User Creating Event', [
                'user_data' => $user->toArray(),
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
            ]);
        });

        User::created(function ($user) {
            Log::info('User Created Event', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email
            ]);
        });

        User::updating(function ($user) {
            Log::info('User Updating Event', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'dirty_attributes' => $user->getDirty(),
                'original_attributes' => $user->getOriginal(),
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
            ]);
        });

        User::updated(function ($user) {
            Log::info('User Updated Event', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'changes' => $user->getChanges()
            ]);
        });

        User::deleted(function ($user) {
            Log::warning('User Deleted Event', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email
            ]);
        });
    }
}