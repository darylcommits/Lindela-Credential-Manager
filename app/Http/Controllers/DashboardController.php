<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credential;
use App\Models\CredentialCategory;
use App\Models\OtpRequest;
use App\Models\SystemLog;
use App\Models\CredentialAccessLog;
use App\Models\User;
use App\Services\CredentialService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $credentialService;

    public function __construct(CredentialService $credentialService)
    {
        $this->middleware(['auth', 'user.active', 'track.activity']);
        $this->credentialService = $credentialService;
    }

    public function index()
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login');
            }

            $data = [
                'totalCredentials' => Credential::active()->count(),
                'totalCategories' => CredentialCategory::active()->count(),
                'expiringCredentials' => $this->credentialService->getExpiringCredentials(30)->count(),
                'expiredCredentials' => $this->credentialService->getExpiredCredentials()->count(),
            ];

            // Get notifications for the notification bell
            $data['notifications'] = $this->getNotifications($user);
            $data['unreadNotificationsCount'] = $this->getUnreadNotificationsCount($user);

            if ($user->isAdmin()) {
                $data['pendingOtpRequests'] = OtpRequest::pending()
                    ->where('expires_at', '>', now())
                    ->count();

                // Enhanced recent activity for admins
                $data['recentActivity'] = $this->getAdminRecentActivity();

                // Fixed query for MySQL strict mode
                $data['topAccessedCredentials'] = $this->getTopAccessedCredentials();

                // Add admin-specific stats
                $data['todayStats'] = $this->getTodayStats();
                $data['systemAlerts'] = $this->getSystemAlerts();

            } else {
                // Enhanced recent activity for regular users
                $data['myRecentAccess'] = $this->getUserRecentActivity($user);

                $data['myOtpRequests'] = $user->otpRequests()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                // User-specific stats
                $data['userStats'] = $this->getUserStats($user);
            }

            $data['categoriesWithCounts'] = CredentialCategory::withCount('activeCredentials')
                ->active()
                ->orderBy('name')
                ->get();

            // Add system health for all users
            $data['systemHealth'] = $this->getSystemHealth();

            return view('dashboard', $data);

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('dashboard')->with('error', 'Unable to load dashboard. Please try again.');
        }
    }

    /**
     * Get notifications for the notification bell
     */
    private function getNotifications($user)
    {
        $notifications = collect();

        try {
            if ($user->isAdmin()) {
                // Pending OTP requests
                $pendingOtps = OtpRequest::with('user')
                    ->pending()
                    ->where('expires_at', '>', now())
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($otp) {
                        return [
                            'id' => 'otp_' . $otp->id,
                            'type' => 'otp_request',
                            'title' => 'New OTP Request',
                            'message' => ($otp->user ? $otp->user->name : 'Unknown User') . ' requested OTP access',
                            'time' => $otp->created_at,
                            'read' => false,
                            'url' => route('admin.otp.show', $otp),
                            'icon' => 'key',
                            'color' => 'yellow'
                        ];
                    });

                // Expiring credentials
                $expiringCredentials = Credential::active()
                    ->where('expires_at', '<=', now()->addDays(7))
                    ->where('expires_at', '>', now())
                    ->orderBy('expires_at', 'asc')
                    ->limit(5)
                    ->get()
                    ->map(function ($credential) {
                        return [
                            'id' => 'expire_' . $credential->id,
                            'type' => 'credential_expiring',
                            'title' => 'Credential Expiring Soon',
                            'message' => $credential->name . ' expires ' . $credential->expires_at->diffForHumans(),
                            'time' => $credential->expires_at,
                            'read' => false,
                            'url' => route('credentials.show', $credential),
                            'icon' => 'exclamation-triangle',
                            'color' => 'orange'
                        ];
                    });

                // Failed login attempts (from system logs)
                $failedLogins = SystemLog::where('action', 'like', '%failed%')
                    ->orWhere('action', 'like', '%unauthorized%')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get()
                    ->map(function ($log) {
                        return [
                            'id' => 'security_' . $log->id,
                            'type' => 'security_alert',
                            'title' => 'Security Alert',
                            'message' => 'Suspicious activity detected: ' . $log->action,
                            'time' => $log->created_at,
                            'read' => false,
                            'url' => route('admin.logs.system'),
                            'icon' => 'shield-exclamation',
                            'color' => 'red'
                        ];
                    });

                // System maintenance alerts
                $systemIssues = $this->getSystemIssueNotifications();

                $notifications = $notifications->merge($pendingOtps)
                    ->merge($expiringCredentials)
                    ->merge($failedLogins)
                    ->merge($systemIssues);

            } else {
                // User-specific notifications
                
                // OTP status updates
                $latestOtp = $user->otpRequests()
                    ->whereIn('status', ['approved', 'denied'])
                    ->where('updated_at', '>=', now()->subHours(24))
                    ->orderBy('updated_at', 'desc')
                    ->first();

                if ($latestOtp) {
                    $notifications->push([
                        'id' => 'otp_status_' . $latestOtp->id,
                        'type' => 'otp_status',
                        'title' => 'OTP Request ' . ucfirst($latestOtp->status),
                        'message' => 'Your OTP request has been ' . $latestOtp->status,
                        'time' => $latestOtp->updated_at,
                        'read' => false,
                        'url' => route('otp.status'),
                        'icon' => $latestOtp->status === 'approved' ? 'check-circle' : 'x-circle',
                        'color' => $latestOtp->status === 'approved' ? 'green' : 'red'
                    ]);
                }

                // Credentials about to expire that user accessed
                $userCredentials = CredentialAccessLog::where('user_id', $user->id)
                    ->whereHas('credential', function ($query) {
                        $query->where('expires_at', '<=', now()->addDays(7))
                              ->where('expires_at', '>', now())
                              ->where('is_active', true);
                    })
                    ->with('credential')
                    ->distinct('credential_id')
                    ->get()
                    ->map(function ($log) {
                        if (!$log->credential) return null;
                        
                        return [
                            'id' => 'user_expire_' . $log->credential->id,
                            'type' => 'credential_expiring',
                            'title' => 'Your Credential Expiring',
                            'message' => $log->credential->name . ' expires ' . $log->credential->expires_at->diffForHumans(),
                            'time' => $log->credential->expires_at,
                            'read' => false,
                            'url' => route('credentials.show', $log->credential),
                            'icon' => 'clock',
                            'color' => 'yellow'
                        ];
                    })
                    ->filter();

                // OTP session expiry warning
                if ($user->hasValidOtp() && $user->otp_session_expires_at) {
                    $minutesUntilExpiry = $user->otp_session_expires_at->diffInMinutes(now());
                    if ($minutesUntilExpiry <= 30) {
                        $notifications->push([
                            'id' => 'otp_expiry_' . $user->id,
                            'type' => 'otp_expiring',
                            'title' => 'OTP Session Expiring',
                            'message' => 'Your OTP session expires in ' . $minutesUntilExpiry . ' minutes',
                            'time' => $user->otp_session_expires_at,
                            'read' => false,
                            'url' => route('otp.request'),
                            'icon' => 'clock',
                            'color' => 'orange'
                        ]);
                    }
                }

                $notifications = $notifications->merge($userCredentials);
            }

            return $notifications->sortByDesc('time')->take(15)->values();

        } catch (\Exception $e) {
            Log::error('Error getting notifications: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get system issue notifications for admins
     */
    private function getSystemIssueNotifications()
    {
        $notifications = collect();

        // Check for credentials without recent access (stale credentials)
        $staleCredentials = Credential::active()
            ->where(function ($query) {
                $query->whereNull('last_accessed_at')
                      ->orWhere('last_accessed_at', '<', now()->subMonths(6));
            })
            ->count();

        if ($staleCredentials > 10) {
            $notifications->push([
                'id' => 'stale_credentials',
                'type' => 'system_maintenance',
                'title' => 'Stale Credentials Detected',
                'message' => "{$staleCredentials} credentials haven't been accessed in 6+ months",
                'time' => now(),
                'read' => false,
                'url' => route('admin.reports.usage'),
                'icon' => 'exclamation-triangle',
                'color' => 'yellow'
            ]);
        }

        return $notifications;
    }

    /**
     * Get count of unread notifications
     */
    private function getUnreadNotificationsCount($user)
    {
        $count = 0;

        try {
            if ($user->isAdmin()) {
                $count += OtpRequest::pending()->where('expires_at', '>', now())->count();
                $count += Credential::active()
                    ->where('expires_at', '<=', now()->addDays(7))
                    ->where('expires_at', '>', now())
                    ->count();
                
                // Add failed login attempts
                $count += SystemLog::where('action', 'like', '%failed%')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->count();

            } else {
                $count += $user->otpRequests()
                    ->whereIn('status', ['approved', 'denied'])
                    ->where('updated_at', '>=', now()->subHours(24))
                    ->count();

                // Count expiring credentials user accessed
                $count += CredentialAccessLog::where('user_id', $user->id)
                    ->whereHas('credential', function ($query) {
                        $query->where('expires_at', '<=', now()->addDays(7))
                              ->where('expires_at', '>', now())
                              ->where('is_active', true);
                    })
                    ->distinct('credential_id')
                    ->count();

                // Count OTP session expiry warning
                if ($user->hasValidOtp() && $user->otp_session_expires_at) {
                    $minutesUntilExpiry = $user->otp_session_expires_at->diffInMinutes(now());
                    if ($minutesUntilExpiry <= 30) {
                        $count += 1;
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Error counting unread notifications: ' . $e->getMessage());
        }

        return $count;
    }

    /**
     * Get enhanced recent activity for admins
     */
    private function getAdminRecentActivity()
    {
        $activities = collect();

        try {
            // Recent credential access
            $credentialAccess = CredentialAccessLog::with(['user', 'credential'])
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get()
                ->map(function ($log) {
                    if (!$log->user || !$log->credential) return null;
                    
                    return [
                        'type' => 'credential_access',
                        'icon' => $this->getAccessIcon($log->action),
                        'color' => $this->getAccessColor($log->action),
                        'title' => $log->user->name . ' ' . $log->action . ' credential',
                        'subtitle' => $log->credential->name,
                        'time' => $log->created_at,
                        'url' => route('admin.logs.credential-access')
                    ];
                })
                ->filter();

            // Recent system activity
            $systemActivity = SystemLog::with('user')
                ->whereIn('action', ['credential_created', 'credential_deleted', 'otp_approved', 'otp_denied', 'user_created'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'type' => 'system_activity',
                        'icon' => $this->getSystemIcon($log->action),
                        'color' => $this->getSystemColor($log->action),
                        'title' => $log->user ? $log->user->name : 'System',
                        'subtitle' => ucwords(str_replace('_', ' ', $log->action)),
                        'time' => $log->created_at,
                        'url' => route('admin.logs.system')
                    ];
                });

            // Recent OTP activities
            $otpActivity = OtpRequest::with(['user', 'approver'])
                ->whereIn('status', ['approved', 'denied', 'used'])
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($otp) {
                    $actionMap = [
                        'approved' => 'approved OTP for',
                        'denied' => 'denied OTP for',
                        'used' => 'used OTP'
                    ];
                    
                    return [
                        'type' => 'otp_activity',
                        'icon' => $otp->status === 'approved' ? 'check-circle' : ($otp->status === 'denied' ? 'x-circle' : 'key'),
                        'color' => $otp->status === 'approved' ? 'green' : ($otp->status === 'denied' ? 'red' : 'blue'),
                        'title' => $otp->approver ? $otp->approver->name : ($otp->user ? $otp->user->name : 'User'),
                        'subtitle' => $actionMap[$otp->status] . ($otp->user && $otp->approver ? ' ' . $otp->user->name : ''),
                        'time' => $otp->updated_at,
                        'url' => route('admin.otp.show', $otp)
                    ];
                });

            return $activities->merge($credentialAccess)
                ->merge($systemActivity)
                ->merge($otpActivity)
                ->sortByDesc('time')
                ->take(10)
                ->values();

        } catch (\Exception $e) {
            Log::error('Error getting admin recent activity: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get enhanced recent activity for users
     */
    private function getUserRecentActivity($user)
    {
        try {
            return CredentialAccessLog::with(['credential.category'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($log) {
                    if (!$log->credential) return null;
                    
                    return [
                        'type' => 'my_access',
                        'icon' => $this->getAccessIcon($log->action),
                        'color' => $this->getAccessColor($log->action),
                        'title' => 'You ' . $log->action . ' credential',
                        'subtitle' => $log->credential->name,
                        'time' => $log->created_at,
                        'url' => route('credentials.show', $log->credential)
                    ];
                })
                ->filter()
                ->values();

        } catch (\Exception $e) {
            Log::error('Error getting user recent activity: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get top accessed credentials for admin dashboard
     */
    private function getTopAccessedCredentials()
    {
        try {
            return Credential::with(['category'])
                ->select('credentials.id', 'credentials.name', 'credentials.category_id')
                ->join('credential_access_logs', 'credentials.id', '=', 'credential_access_logs.credential_id')
                ->where('credential_access_logs.created_at', '>=', now()->subDays(30))
                ->where('credentials.is_active', true)
                ->groupBy('credentials.id', 'credentials.name', 'credentials.category_id')
                ->orderByRaw('COUNT(credential_access_logs.id) DESC')
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            Log::error('Error getting top accessed credentials: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get today's statistics for admin dashboard
     */
    private function getTodayStats()
    {
        $today = now()->startOfDay();
        
        try {
            return [
                'credentials_accessed_today' => CredentialAccessLog::where('created_at', '>=', $today)->count(),
                'unique_users_today' => CredentialAccessLog::where('created_at', '>=', $today)
                    ->distinct('user_id')->count('user_id'),
                'otp_requests_today' => OtpRequest::where('created_at', '>=', $today)->count(),
                'new_credentials_today' => Credential::where('created_at', '>=', $today)->count(),
                'failed_logins_today' => SystemLog::where('action', 'like', '%failed%')
                    ->where('created_at', '>=', $today)->count(),
            ];

        } catch (\Exception $e) {
            Log::error('Error getting today stats: ' . $e->getMessage());
            return [
                'credentials_accessed_today' => 0,
                'unique_users_today' => 0,
                'otp_requests_today' => 0,
                'new_credentials_today' => 0,
                'failed_logins_today' => 0,
            ];
        }
    }

    /**
     * Get user-specific statistics
     */
    private function getUserStats($user)
    {
        try {
            $lastAccess = CredentialAccessLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $favoriteCredentials = CredentialAccessLog::where('user_id', $user->id)
                ->select('credential_id', DB::raw('COUNT(*) as access_count'))
                ->groupBy('credential_id')
                ->orderBy('access_count', 'desc')
                ->with('credential')
                ->limit(3)
                ->get();

            return [
                'total_access_count' => CredentialAccessLog::where('user_id', $user->id)->count(),
                'access_this_week' => CredentialAccessLog::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subWeek())->count(),
                'access_today' => CredentialAccessLog::where('user_id', $user->id)
                    ->whereDate('created_at', today())->count(),
                'last_access' => $lastAccess,
                'favorite_credentials' => $favoriteCredentials,
                'otp_session_active' => $user->hasValidOtp(),
                'otp_session_expires_at' => $user->otp_session_expires_at,
                'pending_otp_requests' => $user->otpRequests()
                    ->where('status', 'pending')
                    ->where('expires_at', '>', now())
                    ->count(),
            ];

        } catch (\Exception $e) {
            Log::error('Error getting user stats: ' . $e->getMessage());
            return [
                'total_access_count' => 0,
                'access_this_week' => 0,
                'access_today' => 0,
                'last_access' => null,
                'favorite_credentials' => collect(),
                'otp_session_active' => false,
                'otp_session_expires_at' => null,
                'pending_otp_requests' => 0,
            ];
        }
    }

    /**
     * Get system alerts for admin dashboard
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        try {
            // Check for expired credentials
            $expiredCount = Credential::active()->where('expires_at', '<', now())->count();
            if ($expiredCount > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'message' => "{$expiredCount} credentials have expired",
                    'url' => route('credentials.index', ['filter' => 'expired'])
                ];
            }

            // Check for expiring credentials
            $expiringCount = Credential::active()
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('expires_at', '>', now())
                ->count();
            if ($expiringCount > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'icon' => 'clock',
                    'message' => "{$expiringCount} credentials expiring within 7 days",
                    'url' => route('credentials.index', ['filter' => 'expiring'])
                ];
            }

            // Check for weak passwords
            $weakPasswordCount = Credential::where('password_strength', '<=', 2)->active()->count();
            if ($weakPasswordCount > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'icon' => 'shield-exclamation',
                    'message' => "{$weakPasswordCount} credentials have weak passwords",
                    'url' => route('admin.reports.security')
                ];
            }

            // Check for pending OTP requests
            $pendingOtpCount = OtpRequest::pending()->where('expires_at', '>', now())->count();
            if ($pendingOtpCount > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'key',
                    'message' => "{$pendingOtpCount} OTP requests pending approval",
                    'url' => route('admin.otp.index')
                ];
            }

            // Check for inactive users with valid OTP sessions
            $staleOtpCount = User::where('otp_session_expires_at', '>', now())
                ->whereDoesntHave('credentialAccessLogs', function ($query) {
                    $query->where('created_at', '>=', now()->subHours(2));
                })
                ->count();
            
            if ($staleOtpCount > 0) {
                $alerts[] = [
                    'type' => 'info',
                    'icon' => 'user-clock',
                    'message' => "{$staleOtpCount} users have unused active OTP sessions",
                    'url' => route('admin.users.index')
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error getting system alerts: ' . $e->getMessage());
        }

        return $alerts;
    }

    /**
     * Get system health status
     */
    private function getSystemHealth()
    {
        try {
            $health = [
                'status' => 'healthy',
                'issues' => [],
                'score' => 100,
                'last_checked' => now()
            ];

            // Check database connectivity
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $health['issues'][] = [
                    'type' => 'database_error',
                    'severity' => 'critical',
                    'message' => 'Database connection failed'
                ];
                $health['score'] -= 50;
            }

            // Check for expired credentials
            $expiredCount = Credential::active()->where('expires_at', '<', now())->count();
            if ($expiredCount > 0) {
                $health['issues'][] = [
                    'type' => 'expired_credentials',
                    'count' => $expiredCount,
                    'severity' => $expiredCount > 10 ? 'high' : 'medium',
                    'message' => "There are {$expiredCount} expired credentials"
                ];
                $health['score'] -= min(30, $expiredCount * 2);
            }

            // Check for expiring credentials
            $expiringCount = Credential::active()
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('expires_at', '>', now())
                ->count();
            if ($expiringCount > 0) {
                $health['issues'][] = [
                    'type' => 'expiring_credentials',
                    'count' => $expiringCount,
                    'severity' => 'low',
                    'message' => "There are {$expiringCount} credentials expiring soon"
                ];
                $health['score'] -= min(15, $expiringCount);
            }

            // Check for pending OTP requests
            $pendingOtpCount = OtpRequest::pending()
                ->where('expires_at', '>', now())
                ->count();
            if ($pendingOtpCount > 0) {
                $health['issues'][] = [
                    'type' => 'pending_otp_requests',
                    'count' => $pendingOtpCount,
                    'severity' => $pendingOtpCount > 5 ? 'medium' : 'low',
                    'message' => "There are {$pendingOtpCount} pending OTP requests"
                ];
                $health['score'] -= min(10, $pendingOtpCount);
            }

            // Determine overall status
            if ($health['score'] >= 90) {
                $health['status'] = 'healthy';
            } elseif ($health['score'] >= 70) {
                $health['status'] = 'warning';
            } else {
                $health['status'] = 'critical';
            }

            return $health;

        } catch (\Exception $e) {
            Log::error('Error getting system health: ' . $e->getMessage());
            return [
                'status' => 'error',
                'issues' => [['type' => 'system_error', 'severity' => 'critical', 'message' => 'Unable to check system health']],
                'score' => 0,
                'last_checked' => now()
            ];
        }
    }

    /**
     * Helper methods for icons and colors
     */
    private function getAccessIcon($action)
    {
        return match($action) {
            'view' => 'eye',
            'copy' => 'clipboard',
            'edit' => 'pencil',
            'delete' => 'trash',
            default => 'key'
        };
    }

    private function getAccessColor($action)
    {
        return match($action) {
            'view' => 'blue',
            'copy' => 'green',
            'edit' => 'yellow',
            'delete' => 'red',
            default => 'gray'
        };
    }

    private function getSystemIcon($action)
    {
        return match($action) {
            'credential_created' => 'plus-circle',
            'credential_deleted' => 'trash',
            'otp_approved' => 'check-circle',
            'otp_denied' => 'x-circle',
            'user_created' => 'user-plus',
            default => 'cog'
        };
    }

    private function getSystemColor($action)
    {
        return match($action) {
            'credential_created' => 'green',
            'credential_deleted' => 'red',
            'otp_approved' => 'green',
            'otp_denied' => 'red',
            'user_created' => 'blue',
            default => 'gray'
        };
    }

    /**
     * API endpoint to get notifications
     */
    public function getNotificationsApi()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $notifications = $this->getNotifications($user);
            $unreadCount = $this->getUnreadNotificationsCount($user);

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
                'success' => true,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getNotificationsApi: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch notifications',
                'success' => false
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request, $id)
    {
        try {
            // For now, we'll just return success
            // In a full implementation, you'd store notification read status in a database table
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to mark notification as read',
                'success' => false
            ], 500);
        }
    }

    /**
     * Get recent activity for API (real-time updates)
     */
    public function getRecentActivityApi()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            if ($user->isAdmin()) {
                $activity = $this->getAdminRecentActivity();
            } else {
                $activity = $this->getUserRecentActivity($user);
            }

            return response()->json([
                'success' => true,
                'activity' => $activity,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getRecentActivityApi: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch recent activity',
                'success' => false
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for API
     */
    public function getDashboardStatsApi()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $stats = [
                'totalCredentials' => Credential::active()->count(),
                'totalCategories' => CredentialCategory::active()->count(),
                'expiringCredentials' => $this->credentialService->getExpiringCredentials(30)->count(),
                'expiredCredentials' => $this->credentialService->getExpiredCredentials()->count(),
            ];

            if ($user->isAdmin()) {
                $stats['todayStats'] = $this->getTodayStats();
                $stats['pendingOtpRequests'] = OtpRequest::pending()
                    ->where('expires_at', '>', now())
                    ->count();
                $stats['systemHealth'] = $this->getSystemHealth();
            } else {
                $stats['userStats'] = $this->getUserStats($user);
                $stats['otpStatus'] = [
                    'hasValidOtp' => $user->hasValidOtp(),
                    'expiresAt' => $user->otp_session_expires_at,
                    'timeRemaining' => $user->otp_session_expires_at ? 
                        max(0, $user->otp_session_expires_at->diffInMinutes(now())) : 0
                ];
            }

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getDashboardStatsApi: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch dashboard stats',
                'success' => false
            ], 500);
        }
    }

    /**
     * Get quick stats for dashboard widgets
     */
    public function getQuickStats()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $stats = [
                'credentials' => [
                    'total' => Credential::active()->count(),
                    'expiring' => Credential::active()
                        ->where('expires_at', '<=', now()->addDays(30))
                        ->where('expires_at', '>', now())
                        ->count(),
                    'expired' => Credential::active()
                        ->where('expires_at', '<', now())
                        ->count(),
                ],
                'categories' => [
                    'total' => CredentialCategory::active()->count(),
                    'with_credentials' => CredentialCategory::active()
                        ->whereHas('activeCredentials')
                        ->count(),
                ],
                'activity' => [
                    'today' => CredentialAccessLog::whereDate('created_at', today())->count(),
                    'this_week' => CredentialAccessLog::where('created_at', '>=', now()->startOfWeek())->count(),
                    'this_month' => CredentialAccessLog::where('created_at', '>=', now()->startOfMonth())->count(),
                ]
            ];

            if ($user->isAdmin()) {
                $stats['otp_requests'] = [
                    'pending' => OtpRequest::pending()->where('expires_at', '>', now())->count(),
                    'today' => OtpRequest::whereDate('created_at', today())->count(),
                    'this_week' => OtpRequest::where('created_at', '>=', now()->startOfWeek())->count(),
                ];
                
                $stats['users'] = [
                    'total' => User::where('is_active', true)->count(),
                    'active_today' => CredentialAccessLog::whereDate('created_at', today())
                        ->distinct('user_id')->count('user_id'),
                ];
            } else {
                $stats['my_activity'] = [
                    'total_access' => CredentialAccessLog::where('user_id', $user->id)->count(),
                    'today' => CredentialAccessLog::where('user_id', $user->id)
                        ->whereDate('created_at', today())->count(),
                    'this_week' => CredentialAccessLog::where('user_id', $user->id)
                        ->where('created_at', '>=', now()->startOfWeek())->count(),
                ];
                
                $stats['otp_status'] = [
                    'has_valid_otp' => $user->hasValidOtp(),
                    'expires_at' => $user->otp_session_expires_at,
                    'pending_requests' => $user->otpRequests()
                        ->where('status', 'pending')
                        ->where('expires_at', '>', now())
                        ->count(),
                ];
            }

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getQuickStats: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to fetch quick stats',
                'success' => false
            ], 500);
        }
    }

    /**
     * Check for new notifications since last check
     */
    public function checkNewNotifications(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $lastCheck = $request->input('last_check', now()->subMinutes(5));
            $hasNewNotifications = false;
            $newCount = 0;

            if ($user->isAdmin()) {
                // Check for new OTP requests
                $newOtpRequests = OtpRequest::pending()
                    ->where('expires_at', '>', now())
                    ->where('created_at', '>', $lastCheck)
                    ->count();

                // Check for new expiring credentials
                $newExpiringCredentials = Credential::active()
                    ->where('expires_at', '<=', now()->addDays(7))
                    ->where('expires_at', '>', now())
                    ->where('updated_at', '>', $lastCheck)
                    ->count();

                $newCount = $newOtpRequests + $newExpiringCredentials;
                $hasNewNotifications = $newCount > 0;

            } else {
                // Check for OTP status updates
                $newOtpUpdates = $user->otpRequests()
                    ->whereIn('status', ['approved', 'denied'])
                    ->where('updated_at', '>', $lastCheck)
                    ->count();

                $newCount = $newOtpUpdates;
                $hasNewNotifications = $newCount > 0;
            }

            return response()->json([
                'success' => true,
                'has_new_notifications' => $hasNewNotifications,
                'new_count' => $newCount,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking new notifications: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to check for new notifications',
                'success' => false
            ], 500);
        }
    }
}