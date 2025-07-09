<?php

// File: app/Http/Controllers/Admin/SystemLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemLog;
use App\Models\CredentialAccessLog;
use App\Models\User;
use App\Models\Credential;
use Illuminate\Support\Facades\DB;

class SystemLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.active', 'admin', 'track.activity']);
    }

    /**
     * Display system logs with enhanced functionality
     */
    public function index(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'type' => 'nullable|string', // Frontend sends 'type' not 'module'
            'page' => 'nullable|integer|min:1'
        ]);

        // Check if we should show CredentialAccessLogs or SystemLogs
        $useCredentialLogs = !SystemLog::exists() || $request->input('source') === 'credential_access';
        
        if ($useCredentialLogs) {
            // Use CredentialAccessLog as fallback/primary source
            $query = CredentialAccessLog::with(['user', 'credential', 'otpRequest']);
            
            // Apply date filtering (date-only using whereDate)
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Apply type filtering (map to action patterns)
            if ($request->filled('type')) {
                switch ($request->type) {
                    case 'credential':
                        $query->whereIn('action', ['view', 'copy', 'edit']);
                        break;
                    case 'user':
                        // Filter for user-related actions (if any)
                        $query->where('action', 'like', '%user%');
                        break;
                    case 'otp':
                        // Filter for OTP-related actions (if any)
                        $query->where('action', 'like', '%otp%');
                        break;
                    case 'system':
                        $query->whereNotIn('action', ['view', 'copy', 'edit']);
                        break;
                }
            }
            
            // Legacy filters support
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            
            if ($request->filled('level')) {
                // Map level to action patterns for CredentialAccessLog
                switch ($request->level) {
                    case 'error':
                        $query->where('action', 'like', '%fail%');
                        break;
                    case 'warning':
                        $query->where('action', 'like', '%delete%');
                        break;
                    case 'info':
                        // Default case, no additional filter
                        break;
                }
            }
            
            $logs = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Transform CredentialAccessLog to match expected format
            $logs->getCollection()->transform(function ($log) {
                $log->level = $this->determineLogLevel($log->action);
                $log->type = $this->determineLogType($log->action);
                $log->module = $log->type; // Set module same as type for frontend compatibility
                $log->description = $log->formatted_action ?? $this->formatLogDescription($log);
                $log->message = $this->formatLogMessage($log);
                return $log;
            });
            
            $modules = ['credential', 'user', 'otp', 'system'];
            
        } else {
            // Use existing SystemLog functionality
            $query = SystemLog::with('user');
            
            // Apply date filtering (date-only using whereDate)
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Apply type filtering (frontend sends 'type', map to 'module')
            if ($request->filled('type')) {
                $query->where('module', $request->type);
            }
            
            // Legacy filters support
            if ($request->filled('module')) {
                $query->byModule($request->module);
            }
            
            if ($request->filled('level')) {
                $query->byLevel($request->level);
            }
            
            if ($request->filled('user_id')) {
                $query->byUser($request->user_id);
            }
            
            $logs = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Transform SystemLog to add required properties
            $logs->getCollection()->transform(function ($log) {
                $log->type = $log->module; // Add type property for frontend compatibility
                $log->message = $log->description; // Use description as message
                return $log;
            });
            
            $modules = SystemLog::distinct()->pluck('module')->sort();
        }

        $levels = ['info', 'warning', 'error', 'critical'];
        $users = User::select('id', 'name')->get();

        // Add debug info if in debug mode
        if (config('app.debug')) {
            $debugInfo = [
                'using_credential_logs' => $useCredentialLogs,
                'total_logs_in_result' => $logs->total(),
                'applied_filters' => [
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'type' => $request->type,
                    'legacy_module' => $request->module,
                    'level' => $request->level,
                    'user_id' => $request->user_id,
                ],
                'system_logs_exist' => SystemLog::exists(),
                'credential_logs_count' => CredentialAccessLog::count(),
            ];
            
            \Log::info('System Logs Debug Info', $debugInfo);
        }

        return view('admin.logs.system', compact('logs', 'modules', 'levels', 'users', 'useCredentialLogs'));
    }

    /**
     * Display credential access logs
     */
    public function credentialAccess(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'type' => 'nullable|string',
            'action' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'credential_id' => 'nullable|integer',
            'page' => 'nullable|integer|min:1'
        ]);

        $query = CredentialAccessLog::with(['credential', 'user', 'otpRequest']);

        // Apply date filtering (date-only using whereDate)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply type filtering (map to actions)
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'view':
                    $query->where('action', 'view');
                    break;
                case 'copy':
                    $query->where('action', 'copy');
                    break;
                case 'edit':
                    $query->where('action', 'edit');
                    break;
                case 'delete':
                    $query->where('action', 'delete');
                    break;
            }
        }

        // Legacy filters support
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('credential_id')) {
            $query->where('credential_id', $request->credential_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Transform logs for frontend compatibility
        $logs->getCollection()->transform(function ($log) {
            $log->type = 'credential'; // Set type for frontend
            $log->level = $this->determineLogLevel($log->action);
            $log->module = 'credential_access';
            $log->message = $this->formatCredentialLogMessage($log);
            return $log;
        });

        // Get available filter options
        $actions = CredentialAccessLog::distinct()
            ->pluck('action')
            ->filter()
            ->sort()
            ->values();
        
        $credentials = Credential::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();

        return view('admin.logs.credential-access', compact('logs', 'actions', 'credentials', 'users'));
    }

    /**
     * Show specific log entry (works for both log types)
     */
    public function show($logType, $id)
    {
        if ($logType === 'system') {
            $log = SystemLog::with('user')->findOrFail($id);
        } else {
            $log = CredentialAccessLog::with(['user', 'credential', 'otpRequest'])->findOrFail($id);
        }

        return view('admin.logs.show', compact('log', 'logType'));
    }

    /**
     * Enhanced export functionality
     */
    public function export(Request $request)
    {
        $logType = $request->input('log_type', 'system');
        
        if ($logType === 'credential_access' || !SystemLog::exists()) {
            return $this->exportCredentialAccessLogs($request);
        }
        
        return $this->exportSystemLogs($request);
    }

    /**
     * Export system logs
     */
    private function exportSystemLogs(Request $request)
    {
        $query = SystemLog::with('user');

        // Apply date filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply type/module filtering (frontend sends 'type')
        if ($request->filled('type')) {
            $query->where('module', $request->type);
        }
        
        // Legacy support
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['Date/Time', 'User', 'Module', 'Action', 'Description', 'Level', 'IP Address'];

        foreach ($logs as $log) {
            $csvData[] = [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->name : 'System',
                $log->module ?? 'Unknown',
                $log->action ?? 'Unknown',
                $log->description ?? 'No description',
                $log->level ?? 'info',
                $log->ip_address ?? 'Unknown',
            ];
        }

        return $this->generateCsvResponse($csvData, 'system-logs');
    }

    /**
     * Export credential access logs
     */
    private function exportCredentialAccessLogs(Request $request)
    {
        $query = CredentialAccessLog::with(['user', 'credential', 'otpRequest']);
        
        // Apply date filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply type filtering
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'view':
                    $query->where('action', 'view');
                    break;
                case 'copy':
                    $query->where('action', 'copy');
                    break;
                case 'edit':
                    $query->where('action', 'edit');
                    break;
                case 'delete':
                    $query->where('action', 'delete');
                    break;
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'ID', 'Date/Time', 'User', 'Credential', 'Action', 'IP Address', 
            'User Agent', 'OTP Request ID', 'Changes'
        ];

        foreach ($logs as $log) {
            $csvData[] = [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->name : 'Unknown',
                $log->credential ? $log->credential->name : 'Unknown',
                $log->action,
                $log->ip_address ?? 'Unknown',
                substr($log->user_agent ?? '', 0, 50) . '...',
                $log->otp_request_id ?? 'N/A',
                $log->changes ? json_encode($log->changes) : 'None'
            ];
        }

        return $this->generateCsvResponse($csvData, 'credential-access-logs');
    }

    /**
     * Generate CSV response
     */
    private function generateCsvResponse($csvData, $filePrefix)
    {
        $filename = $filePrefix . '-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Format log message for display
     */
    private function formatLogMessage($log)
    {
        $user = $log->user ? $log->user->name : 'System';
        $timestamp = $log->created_at->format('g:i A');
        $action = str_replace('_', ' ', $log->action ?? 'action');
        $action = ucwords($action);
        
        return "{$user} - {$action} at {$timestamp}";
    }

    /**
     * Format credential log message
     */
    private function formatCredentialLogMessage($log)
    {
        $user = $log->user ? $log->user->name : 'Unknown User';
        $credential = $log->credential ? $log->credential->name : 'Unknown Credential';
        $action = ucfirst($log->action);
        $timestamp = $log->created_at->format('g:i A');
        
        return "{$user} {$action} '{$credential}' at {$timestamp}";
    }

    /**
     * Format log description
     */
    private function formatLogDescription($log)
    {
        if (isset($log->formatted_action)) {
            return $log->formatted_action;
        }
        
        $user = $log->user ? $log->user->name : 'System';
        $action = str_replace('_', ' ', $log->action ?? 'performed action');
        
        return "{$user} " . strtolower($action);
    }

    // ... (keep all your existing methods like credentialAccessDetail, userAccessLogs, etc.)

    /**
     * Get credential access detail
     */
    public function credentialAccessDetail(Credential $credential, Request $request)
    {
        $period = $request->input('period', 30);
        
        $logs = CredentialAccessLog::where('credential_id', $credential->id)
            ->with(['user', 'otpRequest'])
            ->recent($period)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total_accesses' => CredentialAccessLog::getAccessCountByAction($credential->id),
            'unique_users' => CredentialAccessLog::getUniqueAccessors($credential->id),
            'recent_accesses' => $logs->total(),
        ];

        return view('admin.logs.credential-detail', compact('credential', 'logs', 'stats', 'period'));
    }

    /**
     * Get user access logs
     */
    public function userAccessLogs(User $user, Request $request)
    {
        $period = $request->input('period', 30);
        
        $logs = CredentialAccessLog::where('user_id', $user->id)
            ->with(['credential', 'otpRequest'])
            ->recent($period)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $activitySummary = CredentialAccessLog::where('user_id', $user->id)
            ->recent($period)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->get();

        return view('admin.logs.user-access', compact('user', 'logs', 'activitySummary', 'period'));
    }

    /**
     * Get suspicious activity
     */
    public function suspiciousActivity(Request $request)
    {
        $period = $request->input('period', 7);
        $suspiciousData = CredentialAccessLog::getSuspiciousActivity($period);
        
        $suspiciousLogs = CredentialAccessLog::where('created_at', '>=', now()->subDays($period))
            ->where(function($query) {
                $query->where('action', 'like', '%fail%')
                      ->orWhereRaw('HOUR(created_at) < 9 OR HOUR(created_at) > 18');
            })
            ->with(['user', 'credential'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.logs.suspicious-activity', compact('suspiciousData', 'suspiciousLogs', 'period'));
    }

    /**
     * Cleanup old logs
     */
    public function cleanupOldLogs(Request $request)
    {
        $request->validate([
            'days_to_keep' => 'required|integer|min:30|max:365'
        ]);

        $daysToKeep = $request->input('days_to_keep', 180);
        
        $deletedCredentialLogs = CredentialAccessLog::cleanupOldLogs($daysToKeep);
        $deletedSystemLogs = 0;
        
        // Clean system logs if they exist
        if (SystemLog::exists()) {
            $cutoffDate = now()->subDays($daysToKeep);
            $deletedSystemLogs = SystemLog::where('created_at', '<', $cutoffDate)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted " . ($deletedCredentialLogs + $deletedSystemLogs) . " old log entries",
            'deleted_credential_logs' => $deletedCredentialLogs,
            'deleted_system_logs' => $deletedSystemLogs
        ]);
    }

    /**
     * Get recent logs for API
     */
    public function getRecentLogs(Request $request)
    {
        $minutes = $request->input('minutes', 30);
        
        $logs = CredentialAccessLog::where('created_at', '>=', now()->subMinutes($minutes))
            ->with(['user', 'credential'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'count' => $logs->count(),
            'generated_at' => now()->toISOString()
        ]);
    }

    /**
     * Live monitor page
     */
    public function liveMonitor()
    {
        $recentActivity = CredentialAccessLog::getRecentActivity(30);
        $dashboardMetrics = CredentialAccessLog::getDashboardMetrics();
        
        return view('admin.logs.live-monitor', compact('recentActivity', 'dashboardMetrics'));
    }

    /**
     * Get dashboard stats for API
     */
    public function getDashboardStats()
    {
        $stats = CredentialAccessLog::getDashboardMetrics();
        $accessStats = CredentialAccessLog::getAccessStats(30);
        
        return response()->json([
            'success' => true,
            'dashboard_metrics' => $stats,
            'access_stats' => $accessStats,
            'generated_at' => now()->toISOString()
        ]);
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(Request $request)
    {
        $period = $request->input('period', 30);
        
        $analytics = [
            'access_stats' => CredentialAccessLog::getAccessStats($period),
            'suspicious_activity' => CredentialAccessLog::getSuspiciousActivity($period),
            'dashboard_metrics' => CredentialAccessLog::getDashboardMetrics(),
        ];

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'period_days' => $period,
            'generated_at' => now()->toISOString()
        ]);
    }

    /**
     * Determine log level based on action
     */
    private function determineLogLevel($action)
    {
        if (str_contains($action, 'fail') || str_contains($action, 'error')) {
            return 'error';
        }
        
        if (str_contains($action, 'delete') || str_contains($action, 'remove')) {
            return 'warning';
        }
        
        return 'info';
    }

    /**
     * Determine log type based on action
     */
    private function determineLogType($action)
    {
        if (str_contains($action, 'copy') || $action === 'view' || str_contains($action, 'visit')) {
            return 'credential';
        }
        
        if (str_contains($action, 'otp')) {
            return 'otp';
        }
        
        if (str_contains($action, 'user') || str_contains($action, 'login')) {
            return 'user';
        }
        
        return 'system';
    }
}