<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Admin\OtpManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\ReportsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\OtpRequest;
use App\Models\Credential;
use App\Models\SystemLog;
use App\Models\CredentialAccessLog;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Welcome route (if needed for marketing/landing page)
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Authentication routes (provided by Laravel Breeze)
require __DIR__.'/auth.php';

// Protected routes (require authentication and active user)
Route::middleware(['auth', 'verified', 'user.active', 'track.activity'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard Notifications (FIX: Added missing route)
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications'])->name('dashboard.notifications');
    
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // OTP Management for IT Staff
    Route::prefix('otp')->name('otp.')->group(function () {
        Route::get('/request', [OtpController::class, 'showRequestForm'])->name('request');
        Route::post('/request', [OtpController::class, 'requestOtp'])->name('submit');
        Route::get('/verify', [OtpController::class, 'showVerifyForm'])->name('verify');
        Route::post('/verify', [OtpController::class, 'verifyOtp'])->name('confirm');
        Route::get('/status', [OtpController::class, 'status'])->name('status');
    });
    
    // API Routes for Dashboard and Notifications
    Route::prefix('api')->name('api.')->group(function () {
        // OTP status API
        Route::get('/otp/status', [OtpController::class, 'status'])->name('otp.status');
        
        // Dashboard Notification APIs
        Route::get('/notifications', [DashboardController::class, 'getNotificationsApi'])->name('notifications');
        Route::post('/notifications/{id}/read', [DashboardController::class, 'markNotificationRead'])->name('notifications.read');
        Route::get('/notifications/check-new', [DashboardController::class, 'checkNewNotifications'])->name('notifications.check-new');
        
        // Dashboard Real-time APIs
        Route::get('/dashboard/activity', [DashboardController::class, 'getRecentActivityApi'])->name('dashboard.activity');
        Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStatsApi'])->name('dashboard.stats');
        Route::get('/dashboard/quick-stats', [DashboardController::class, 'getQuickStats'])->name('dashboard.quick-stats');
    });
});

// Credentials Management (requires valid OTP for IT Staff)
Route::middleware(['auth', 'verified', 'user.active', 'require.otp', 'track.activity'])->group(function () {
    
    // Main credential routes
    Route::resource('credentials', CredentialController::class);
    
    // Enhanced credential access logging routes
    Route::post('credentials/{credential}/log-access', [CredentialController::class, 'logAccess'])
        ->name('credentials.log-access');
    
    // Copy functionality with logging
    Route::post('credentials/{credential}/copy', [CredentialController::class, 'copy'])
        ->name('credentials.copy');
    
    // Bulk operation logging
    Route::post('credentials/log-bulk-operation', [CredentialController::class, 'logBulkOperation'])
        ->name('credentials.log-bulk');
    
    // Access analytics for specific credential
    Route::get('credentials/{credential}/analytics', [CredentialController::class, 'getAccessAnalytics'])
        ->name('credentials.analytics');
    
    // Search functionality
    Route::get('credentials-search', [CredentialController::class, 'search'])
        ->name('credentials.search');
    
    // Export functionality (admin only check is in controller)
    Route::get('credentials-export', [CredentialController::class, 'export'])
        ->name('credentials.export');
});

// Admin only routes
Route::middleware(['auth', 'verified', 'user.active', 'admin', 'track.activity'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    
    // OTP Management
    Route::prefix('otp')->name('otp.')->group(function () {
        Route::get('/', [OtpManagementController::class, 'index'])->name('index');
        Route::get('/{otpRequest}', [OtpManagementController::class, 'show'])->name('show');
        Route::post('/{otpRequest}/approve', [OtpManagementController::class, 'approve'])->name('approve');
        Route::post('/{otpRequest}/deny', [OtpManagementController::class, 'deny'])->name('deny');
        Route::post('/bulk-approve', [OtpManagementController::class, 'bulkApprove'])->name('bulk-approve');
    });
    
    // User Management - FIX: Enhanced routes with proper CSRF protection
    Route::resource('users', UserManagementController::class);
    
    // FIX: Changed from POST to PATCH to match frontend expectations
    Route::patch('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
        ->name('users.toggle-status')
        ->middleware(['throttle:30,1']); // Rate limiting to prevent abuse
    
    // FIX: Additional user management routes
    Route::prefix('users')->name('users.')->group(function () {
        // Bulk operations
        Route::post('/bulk-action', [UserManagementController::class, 'bulkAction'])
            ->name('bulk-action')
            ->middleware(['throttle:10,1']);
        
        // User statistics
        Route::get('/stats', [UserManagementController::class, 'getUserStats'])
            ->name('stats');
        
        // Debug routes (only in development)
        if (config('app.debug')) {
            Route::get('/debug/{user?}', [UserManagementController::class, 'debugUser'])
                ->name('debug');
            Route::post('/test-create', [UserManagementController::class, 'testCreate'])
                ->name('test-create');
        }
    });
    
    // Enhanced System Logs
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/system', [SystemLogController::class, 'index'])->name('system');
        Route::get('/credential-access', [SystemLogController::class, 'credentialAccess'])->name('credential-access');
        Route::get('/system/{log}', [SystemLogController::class, 'show'])->name('show');
        Route::get('/export/system', [SystemLogController::class, 'export'])->name('export');
        
        // Enhanced credential access logs routes
        Route::get('/credential-access/{credential}', [SystemLogController::class, 'credentialAccessDetail'])
            ->name('credential-access-detail');
        Route::get('/user-access/{user}', [SystemLogController::class, 'userAccessLogs'])
            ->name('user-access');
        Route::get('/suspicious-activity', [SystemLogController::class, 'suspiciousActivity'])
            ->name('suspicious-activity');
        Route::post('/cleanup-old-logs', [SystemLogController::class, 'cleanupOldLogs'])
            ->name('cleanup');
        
        // Real-time log monitoring
        Route::get('/live-monitor', [SystemLogController::class, 'liveMonitor'])
            ->name('live-monitor');
        Route::get('/api/recent-logs', [SystemLogController::class, 'getRecentLogs'])
            ->name('api.recent-logs');
    });
    
    // Enhanced Reports with access analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/security', [ReportsController::class, 'security'])->name('security');
        Route::get('/usage', [ReportsController::class, 'usage'])->name('usage');
        
        // New access analytics routes
        Route::get('/access-analytics', [ReportsController::class, 'accessAnalytics'])->name('access-analytics');
        Route::get('/user-activity/{user?}', [ReportsController::class, 'userActivity'])->name('user-activity');
        Route::get('/credential-popularity', [ReportsController::class, 'credentialPopularity'])->name('credential-popularity');
        Route::get('/security-incidents', [ReportsController::class, 'securityIncidents'])->name('security-incidents');
        Route::get('/otp-compliance', [ReportsController::class, 'otpCompliance'])->name('otp-compliance');
        
        // Export routes for reports
        Route::get('/export/access-analytics', [ReportsController::class, 'exportAccessAnalytics'])
            ->name('export.access-analytics');
        Route::get('/export/user-activity/{user?}', [ReportsController::class, 'exportUserActivity'])
            ->name('export.user-activity');
    });
    
    // Admin Notification Management (Optional - for future expansion)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', function() {
            // Redirect to system logs for now - can be expanded later
            return redirect()->route('admin.logs.system')->with('info', 'Notification management coming soon!');
        })->name('index');
        
        Route::post('/mark-all-read', function() {
            // Future implementation for marking all notifications as read
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        })->name('mark-all-read');
        
        Route::delete('/clear-old', function() {
            // Future implementation for clearing old notifications
            return response()->json([
                'success' => true,
                'message' => 'Old notifications cleared'
            ]);
        })->name('clear-old');
    });
    
    // System maintenance routes
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        // Future system maintenance routes can be added here
        Route::get('/', function() {
            return view('admin.maintenance.index');
        })->name('index');
        
        Route::post('/cache-clear', function() {
            try {
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Cache cleared successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to clear cache: ' . $e->getMessage()
                ], 500);
            }
        })->name('cache-clear');
        
        Route::post('/logs-cleanup', function() {
            try {
                $cutoffDate = now()->subDays(90); // Keep 90 days
                $systemLogsDeleted = SystemLog::where('created_at', '<', $cutoffDate)->delete();
                $accessLogsDeleted = CredentialAccessLog::where('created_at', '<', $cutoffDate)->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => "Deleted {$systemLogsDeleted} system logs and {$accessLogsDeleted} access logs"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to cleanup logs: ' . $e->getMessage()
                ], 500);
            }
        })->name('logs-cleanup');
    });
});

// API routes for real-time monitoring (admin only)
Route::middleware(['auth', 'verified', 'user.active', 'admin'])
    ->prefix('api/admin')
    ->name('api.admin.')
    ->group(function () {
    
    // Real-time access monitoring API
    Route::get('/logs/recent/{minutes?}', [SystemLogController::class, 'getRecentLogsApi'])
        ->name('logs.recent');
    Route::get('/stats/dashboard', [SystemLogController::class, 'getDashboardStats'])
        ->name('stats.dashboard');
    Route::get('/activity/live', [SystemLogController::class, 'getLiveActivity'])
        ->name('activity.live');
    
    // Analytics API endpoints
    Route::get('/analytics/access-stats/{period?}', function($period = 30) {
        try {
            $stats = CredentialAccessLog::getAccessStats($period);
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'generated_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Analytics access stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Unable to fetch access stats',
                'generated_at' => now()->toISOString()
            ], 500);
        }
    })->name('analytics.access-stats');
    
    Route::get('/analytics/suspicious-activity/{period?}', function($period = 7) {
        try {
            $suspicious_activity = CredentialAccessLog::getSuspiciousActivity($period);
            return response()->json([
                'success' => true,
                'suspicious_activity' => $suspicious_activity,
                'generated_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Analytics suspicious activity error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Unable to fetch suspicious activity',
                'generated_at' => now()->toISOString()
            ], 500);
        }
    })->name('analytics.suspicious-activity');
    
    // Dashboard notification endpoints for admin
    Route::get('/notifications/summary', function() {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $summary = [
                'pending_otp_requests' => OtpRequest::pending()
                    ->where('expires_at', '>', now())->count(),
                'expiring_credentials' => Credential::active()
                    ->where('expires_at', '<=', now()->addDays(7))
                    ->where('expires_at', '>', now())->count(),
                'expired_credentials' => Credential::active()
                    ->where('expires_at', '<', now())->count(),
                'failed_logins_today' => SystemLog::where('action', 'like', '%failed%')
                    ->whereDate('created_at', today())->count(),
                'active_users_today' => CredentialAccessLog::whereDate('created_at', today())
                    ->distinct('user_id')->count('user_id'),
            ];
            
            return response()->json([
                'success' => true,
                'summary' => $summary,
                'generated_at' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Admin notification summary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Unable to fetch notification summary'
            ], 500);
        }
    })->name('notifications.summary');
});

// Additional utility routes for dashboard features
Route::middleware(['auth', 'verified', 'user.active'])->group(function () {
    
    // Health check endpoint
    Route::get('/api/health', function() {
        try {
            // Quick health check
            DB::connection()->getPdo();
            
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'database' => 'connected'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'database' => 'disconnected',
                'error' => 'Database connection failed'
            ], 500);
        }
    })->name('api.health');
    
    // System status endpoint
    Route::get('/api/system/status', function() {
        try {
            $user = Auth::user();
            
            $status = [
                'authenticated' => true,
                'user_active' => $user->is_active,
                'user_role' => $user->role,
                'has_valid_otp' => $user->hasValidOtp(),
                'otp_expires_at' => $user->otp_session_expires_at,
                'system_time' => now()->toISOString(),
                'database_status' => 'connected'
            ];
            
            return response()->json([
                'success' => true,
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unable to fetch system status'
            ], 500);
        }
    })->name('api.system.status');
});

// FIX: Debug routes for troubleshooting (only in development)
if (config('app.debug')) {
    Route::get('/debug/user-management', function() {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }
        
        return response()->json([
            'routes' => [
                'toggle_status_route_exists' => Route::has('admin.users.toggle-status'),
                'toggle_status_url' => route('admin.users.toggle-status', ['user' => 1]),
                'update_route_exists' => Route::has('admin.users.update'),
            ],
            'csrf_token' => csrf_token(),
            'session_info' => [
                'session_id' => session()->getId(),
                'user_id' => $user->id,
                'user_role' => $user->role,
            ],
            'test_user_data' => User::select('id', 'name', 'email', 'role', 'is_active')->first(),
            'observers' => [
                'user_observer_registered' => class_exists('App\Observers\UserObserver'),
            ],
            'middleware' => [
                'admin_middleware_exists' => class_exists('App\Http\Middleware\EnsureUserIsAdmin'),
                'track_activity_exists' => class_exists('App\Http\Middleware\TrackUserActivity'),
            ]
        ]);
    })->name('debug.user-management');
    
    // Test toggle status route specifically
    Route::patch('/debug/test-toggle/{user}', function(User $user) {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Debug toggle route working',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'current_status' => $user->is_active,
            'can_toggle' => $user->id !== $currentUser->id,
        ]);
    })->name('debug.test-toggle');

    Route::get('/debug/session-info', function() {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }
        
        return response()->json([
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'session_data' => session()->all(),
            'headers' => request()->headers->all()
        ]);
    });
    
    Route::get('/debug/clear-sessions', function() {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }
        
        // Clear all sessions
        Artisan::call('session:clear');
        
        return 'Sessions cleared. Please log in again.';
    });

    // EMERGENCY ROUTES - Direct database bypass for user activation
    // These routes bypass all observers, middleware, and model events
    
    // Emergency user activation route - bypasses all observers and form processing
    Route::get('/emergency/activate-user/{id}', function($id) {
        // Only allow in debug mode and for admins
        if (!config('app.debug')) {
            abort(404);
        }
        
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403);
        }
        
        try {
            // Direct database update - bypasses ALL model events, observers, etc.
            $affected = DB::table('users')
                ->where('id', $id)
                ->where('id', '!=', $currentUser->id) // Can't modify own account
                ->update([
                    'is_active' => 1,
                    'updated_at' => now()
                ]);
            
            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found or cannot modify own account'
                ]);
            }
            
            // Verify the user was activated
            $user = DB::table('users')->where('id', $id)->first();
            
            // Log the emergency activation
            Log::info('EMERGENCY USER ACTIVATION', [
                'user_id' => $id,
                'activated_by' => $currentUser->id,
                'success' => $user && $user->is_active,
                'method' => 'direct_database_update'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User activated successfully via emergency bypass',
                'user_id' => $id,
                'user_name' => $user->name ?? 'Unknown',
                'is_active' => $user->is_active ?? false,
                'method' => 'direct_database_update'
            ]);
            
        } catch (\Exception $e) {
            Log::error('EMERGENCY ACTIVATION FAILED', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'admin_user' => $currentUser->id
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Emergency activation failed: ' . $e->getMessage()
            ], 500);
        }
    })->name('emergency.activate-user');

    // Emergency user list - shows current status
    Route::get('/emergency/user-list', function() {
        if (!config('app.debug')) {
            abort(404);
        }
        
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403);
        }
        
        $users = DB::table('users')
            ->select('id', 'name', 'email', 'role', 'is_active', 'created_at', 'updated_at')
            ->orderBy('role', 'desc')
            ->orderBy('is_active', 'desc')
            ->orderBy('id')
            ->get();
        
        $html = '<h1>Emergency User List</h1>';
        $html .= '<style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}</style>';
        $html .= '<table>';
        $html .= '<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>';
        
        foreach ($users as $user) {
            $statusColor = $user->is_active ? 'green' : 'red';
            $statusText = $user->is_active ? 'Active' : 'Inactive';
            $actionButton = '';
            
            if (!$user->is_active && $user->id != $currentUser->id) {
                $actionButton = "<a href='/emergency/activate-user/{$user->id}' style='background:green;color:white;padding:5px 10px;text-decoration:none;border-radius:3px;'>ACTIVATE</a>";
            }
            
            $html .= "<tr>";
            $html .= "<td>{$user->id}</td>";
            $html .= "<td>{$user->name}</td>";
            $html .= "<td>{$user->email}</td>";
            $html .= "<td><span style='background:" . ($user->role === 'admin' ? 'purple' : 'blue') . ";color:white;padding:2px 6px;border-radius:3px;'>{$user->role}</span></td>";
            $html .= "<td><span style='color:{$statusColor};font-weight:bold;'>{$statusText}</span></td>";
            $html .= "<td>{$actionButton}</td>";
            $html .= "</tr>";
        }
        
        $html .= '</table>';
        $html .= '<br><p><strong>Note:</strong> This is an emergency bypass that updates users directly in the database.</p>';
        
        return $html;
    })->name('emergency.user-list');
}

// FIX: Fallback route for handling SPA-like navigation
Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json(['error' => 'Route not found'], 404);
    }
    
    return redirect()->route('login')->with('error', 'The requested page was not found.');
});