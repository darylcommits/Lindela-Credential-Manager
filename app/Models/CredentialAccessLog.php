<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CredentialAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'credential_id',
        'user_id',
        'otp_request_id',
        'action',
        'ip_address',
        'user_agent',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function otpRequest(): BelongsTo
    {
        return $this->belongsTo(OtpRequest::class);
    }

    // Scopes
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCredential($query, $credentialId)
    {
        return $query->where('credential_id', $credentialId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByOtpRequest($query, $otpRequestId)
    {
        return $query->where('otp_request_id', $otpRequestId);
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeSuspicious($query)
    {
        return $query->where(function($q) {
            $q->where('action', 'like', '%fail%')
              ->orWhere('action', 'like', '%error%')
              ->orWhereRaw('HOUR(created_at) < 9 OR HOUR(created_at) > 18');
        });
    }

    // Helper methods - Updated for dark theme
    public function getActionBadgeColorAttribute()
    {
        return match(true) {
            str_contains($this->action, 'copy') => 'bg-orange-500/20 text-orange-400',
            $this->action === 'view' => 'bg-blue-500/20 text-blue-400',
            str_contains($this->action, 'edit') => 'bg-green-500/20 text-green-400',
            str_contains($this->action, 'delete') => 'bg-red-500/20 text-red-400',
            str_contains($this->action, 'export') => 'bg-purple-500/20 text-purple-400',
            str_contains($this->action, 'visit') => 'bg-cyan-500/20 text-cyan-400',
            str_contains($this->action, 'fail') => 'bg-red-600/30 text-red-300',
            str_contains($this->action, 'modal') => 'bg-indigo-500/20 text-indigo-400',
            str_contains($this->action, 'page_') => 'bg-gray-500/20 text-gray-400',
            default => 'bg-gray-500/20 text-gray-400',
        };
    }

    public function getFormattedActionAttribute(): string
    {
        return match($this->action) {
            'view' => 'Viewed credential',
            'copy_password' => 'Copied password',
            'copy_username' => 'Copied username', 
            'copy_email' => 'Copied email',
            'copy_backup_code' => 'Copied backup code',
            'visit_website' => 'Visited website',
            'edit' => 'Edited credential',
            'delete' => 'Deleted credential',
            'export' => 'Exported credential',
            'copy_failed' => 'Failed to copy',
            'page_leave' => 'Left page',
            'page_focus' => 'Returned to page',
            'page_blur' => 'Switched away from page',
            'delete_modal_opened' => 'Opened delete modal',
            'delete_modal_closed' => 'Closed delete modal',
            'user_idle' => 'User became idle',
            'bulk_export' => 'Bulk exported credentials',
            'bulk_delete' => 'Bulk deleted credentials',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    // Enhanced static logging method with better error handling
    public static function logAccess($credentialId, $userId, $action, $otpRequestId = null, $changes = null)
    {
        try {
            return self::create([
                'credential_id' => $credentialId,
                'user_id' => $userId,
                'otp_request_id' => $otpRequestId,
                'action' => $action,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'changes' => $changes,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log credential access: ' . $e->getMessage(), [
                'credential_id' => $credentialId,
                'user_id' => $userId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // Enhanced detailed logging method
    public static function logDetailedAccess($credentialId, $action, $additionalData = [])
    {
        try {
            $user = Auth::user();
            $otpRequest = null;

            // Try to find current OTP request if user exists
            if ($user) {
                $otpRequest = $user->otpRequests()
                    ->where('status', 'approved')
                    ->where('expires_at', '>', now())
                    ->whereNull('used_at')
                    ->first();
            }

            $defaultData = [
                'timestamp' => now()->toISOString(),
                'session_id' => request()->session() ? request()->session()->getId() : null,
                'referrer' => request()->header('referer'),
                'user_name' => $user ? $user->name : 'Guest',
            ];

            $mergedData = array_merge($defaultData, $additionalData);

            return self::logAccess(
                $credentialId,
                $user ? $user->id : null,
                $action,
                $otpRequest ? $otpRequest->id : null,
                $mergedData
            );
        } catch (\Exception $e) {
            Log::error('Failed to log detailed credential access: ' . $e->getMessage(), [
                'credential_id' => $credentialId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // Analytics and statistics methods
    public static function getAccessCountByAction($credentialId, $action = null)
    {
        $query = static::where('credential_id', $credentialId);
        
        if ($action) {
            $query->where('action', $action);
        }
        
        return $query->count();
    }

    public static function getUniqueAccessors($credentialId)
    {
        return static::where('credential_id', $credentialId)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count();
    }

    public static function getAccessStats($period = 30)
    {
        $query = static::where('created_at', '>=', now()->subDays($period));
        
        return [
            'total_accesses' => $query->count(),
            'unique_users' => $query->distinct('user_id')->count(),
            'unique_ips' => $query->distinct('ip_address')->count(),
            'unique_credentials' => $query->distinct('credential_id')->count(),
            'most_accessed_credential' => static::getMostAccessedCredential($period),
            'actions_breakdown' => static::getActionsBreakdown($period),
            'hourly_distribution' => static::getHourlyDistribution($period),
            'otp_usage_stats' => static::getOtpUsageStats($period),
        ];
    }

    public static function getMostAccessedCredential($period = 30)
    {
        return static::where('created_at', '>=', now()->subDays($period))
            ->with('credential')
            ->selectRaw('credential_id, COUNT(*) as access_count')
            ->groupBy('credential_id')
            ->orderByDesc('access_count')
            ->first();
    }

    public static function getActionsBreakdown($period = 30)
    {
        return static::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'action')
            ->toArray();
    }

    public static function getHourlyDistribution($period = 30)
    {
        return static::where('created_at', '>=', now()->subDays($period))
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();
    }

    public static function getOtpUsageStats($period = 30)
    {
        $total = static::where('created_at', '>=', now()->subDays($period))->count();
        $withOtp = static::where('created_at', '>=', now()->subDays($period))
            ->whereNotNull('otp_request_id')
            ->count();

        return [
            'total_accesses' => $total,
            'with_otp' => $withOtp,
            'without_otp' => $total - $withOtp,
            'otp_percentage' => $total > 0 ? round(($withOtp / $total) * 100, 2) : 0,
        ];
    }

    // Security analysis methods
    public static function getSuspiciousActivity($period = 7)
    {
        $suspiciousLogs = collect();

        // Multiple failed attempts from same IP
        $failedAttempts = static::where('created_at', '>=', now()->subDays($period))
            ->where('action', 'like', '%fail%')
            ->selectRaw('ip_address, COUNT(*) as failed_count')
            ->groupBy('ip_address')
            ->having('failed_count', '>', 5)
            ->get();

        // Access outside business hours (assuming 9 AM - 6 PM)
        $afterHoursAccess = static::where('created_at', '>=', now()->subDays($period))
            ->whereRaw('HOUR(created_at) < 9 OR HOUR(created_at) > 18')
            ->count();

        // Multiple IPs for same user in short time
        $multipleIpUsers = static::where('created_at', '>=', now()->subHours(2))
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(DISTINCT ip_address) as ip_count')
            ->groupBy('user_id')
            ->having('ip_count', '>', 2)
            ->with('user')
            ->get();

        return [
            'failed_attempts_by_ip' => $failedAttempts,
            'after_hours_access_count' => $afterHoursAccess,
            'multiple_ip_users' => $multipleIpUsers,
        ];
    }

    public function getUserActivitySummary($userId, $period = 30)
    {
        return static::byUser($userId)
            ->recent($period)
            ->selectRaw('action, COUNT(*) as count, MAX(created_at) as last_action')
            ->groupBy('action')
            ->orderByDesc('count')
            ->get();
    }

    // Security check methods
    public function isSuspicious(): bool
    {
        $suspiciousPatterns = [
            'copy_failed',
            'multiple_copy_attempts',
            'unusual_time_access',
            'multiple_ip_access',
        ];

        return in_array($this->action, $suspiciousPatterns) || 
               $this->isAfterHours() || 
               $this->hasMultipleRecentFailures();
    }

    public function isAfterHours(): bool
    {
        $hour = $this->created_at->hour;
        return $hour < 9 || $hour > 18;
    }

    public function hasMultipleRecentFailures(): bool
    {
        return static::where('ip_address', $this->ip_address)
            ->where('action', 'like', '%fail%')
            ->where('created_at', '>=', now()->subHour())
            ->count() > 3;
    }

    // Utility methods for data export and reporting
    public static function getDataForExport($filters = [])
    {
        try {
            $query = static::with(['credential', 'user', 'otpRequest']);

            if (isset($filters['start_date'])) {
                $query->where('created_at', '>=', $filters['start_date']);
            }

            if (isset($filters['end_date'])) {
                $query->where('created_at', '<=', $filters['end_date']);
            }

            if (isset($filters['action'])) {
                $query->where('action', $filters['action']);
            }

            if (isset($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }

            if (isset($filters['credential_id'])) {
                $query->where('credential_id', $filters['credential_id']);
            }

            return $query->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Failed to get data for export: ' . $e->getMessage());
            return collect();
        }
    }

    public function toExportArray(): array
    {
        try {
            return [
                'id' => $this->id,
                'credential_name' => $this->credential->name ?? 'Unknown',
                'user_name' => $this->user->name ?? 'Unknown',
                'action' => $this->formatted_action,
                'ip_address' => $this->ip_address,
                'user_agent' => $this->user_agent,
                'otp_request_id' => $this->otp_request_id,
                'timestamp' => $this->created_at->format('Y-m-d H:i:s'),
                'changes' => $this->changes ? json_encode($this->changes) : null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to convert log to export array: ' . $e->getMessage());
            return [
                'id' => $this->id ?? 'Unknown',
                'credential_name' => 'Error',
                'user_name' => 'Error',
                'action' => 'Error',
                'ip_address' => 'Error',
                'user_agent' => 'Error',
                'otp_request_id' => null,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'changes' => null,
            ];
        }
    }

    // Real-time monitoring methods
    public static function getRecentActivity($minutes = 30)
    {
        try {
            return static::where('created_at', '>=', now()->subMinutes($minutes))
                ->with(['credential', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get recent activity: ' . $e->getMessage());
            return collect();
        }
    }

    public static function getDashboardMetrics()
    {
        try {
            $today = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();

            return [
                'today_accesses' => static::where('created_at', '>=', $today)->count(),
                'yesterday_accesses' => static::where('created_at', '>=', $yesterday)
                    ->where('created_at', '<', $today)->count(),
                'active_users_today' => static::where('created_at', '>=', $today)
                    ->distinct('user_id')->count(),
                'most_active_credential_today' => static::where('created_at', '>=', $today)
                    ->with('credential')
                    ->selectRaw('credential_id, COUNT(*) as access_count')
                    ->groupBy('credential_id')
                    ->orderByDesc('access_count')
                    ->first(),
                'suspicious_activity_today' => static::where('created_at', '>=', $today)
                    ->suspicious()
                    ->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get dashboard metrics: ' . $e->getMessage());
            return [
                'today_accesses' => 0,
                'yesterday_accesses' => 0,
                'active_users_today' => 0,
                'most_active_credential_today' => null,
                'suspicious_activity_today' => 0,
            ];
        }
    }

    // Helper method to safely get current user
    public static function getCurrentUser()
    {
        try {
            return Auth::user();
        } catch (\Exception $e) {
            Log::warning('Failed to get current user in CredentialAccessLog: ' . $e->getMessage());
            return null;
        }
    }

    // Helper method to safely get session ID
    public static function getSessionId()
    {
        try {
            $session = request()->session();
            return $session ? $session->getId() : null;
        } catch (\Exception $e) {
            Log::warning('Failed to get session ID: ' . $e->getMessage());
            return null;
        }
    }

    // Helper method to get current OTP request safely
    public static function getCurrentOtpRequest($userId)
    {
        try {
            // Check if OtpRequest class exists
            if (!class_exists('App\Models\OtpRequest')) {
                return null;
            }

            return \App\Models\OtpRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->where('expires_at', '>', now())
                ->whereNull('used_at')
                ->first();
        } catch (\Exception $e) {
            Log::warning('Failed to get current OTP request: ' . $e->getMessage());
            return null;
        }
    }

    // Helper method to check if User model has otpRequests relationship
    public static function userHasOtpRelationship($user)
    {
        try {
            return $user && method_exists($user, 'otpRequests');
        } catch (\Exception $e) {
            Log::warning('Failed to check OTP relationship: ' . $e->getMessage());
            return false;
        }
    }

    // Cleanup methods
    public static function cleanupOldLogs($daysToKeep = 180)
    {
        try {
            $cutoffDate = now()->subDays($daysToKeep);
            
            $deletedCount = static::where('created_at', '<', $cutoffDate)->delete();
            
            Log::info("Cleaned up {$deletedCount} old credential access logs older than {$daysToKeep} days");
            
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old logs: ' . $e->getMessage());
            return 0;
        }
    }
}