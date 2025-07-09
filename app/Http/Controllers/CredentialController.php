<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credential;
use App\Models\CredentialCategory;
use App\Models\CredentialAccessLog;
use App\Services\CredentialService;
use App\Services\OtpService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CredentialController extends Controller
{
    protected $credentialService;
    protected $otpService;

    public function __construct(CredentialService $credentialService, OtpService $otpService)
    {
        $this->middleware(['auth', 'user.active', 'track.activity']);
        $this->middleware('valid.otp')->except(['index', 'search']);
        
        $this->credentialService = $credentialService;
        $this->otpService = $otpService;
    }

    public function index(Request $request)
    {
        $categories = CredentialCategory::active()->orderBy('name')->get();
        
        $credentials = Credential::with(['category', 'creator'])
            ->active()
            ->when($request->category_id, function ($query) use ($request) {
                return $query->byCategory($request->category_id);
            })
            ->when($request->search, function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('username', 'like', "%{$request->search}%")
                      ->orWhere('website_url', 'like', "%{$request->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('credentials.index', compact('credentials', 'categories'));
    }

    public function create()
    {
        $categories = CredentialCategory::active()->orderBy('name')->get();
        return view('credentials.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:credential_categories,id',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'website_url' => 'nullable|url|max:500',
            'backup_codes' => 'nullable|array',
            'backup_codes.*' => 'string|max:100',
            'expires_at' => 'nullable|date|after:today',
            'password_never_expires' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        $data = $validator->validated();
        
        // Process backup codes
        if ($request->has('backup_codes')) {
            $data['backup_codes'] = array_filter($request->backup_codes, function ($code) {
                return !empty(trim($code));
            });
        }

        $credential = $this->credentialService->createCredential($data, $user);

        // Log credential creation
        CredentialAccessLog::logDetailedAccess($credential->id, 'create', [
            'credential_name' => $credential->name,
            'credential_category' => $credential->category->name ?? 'Uncategorized',
            'action_type' => 'creation',
        ]);

        return redirect()->route('credentials.show', $credential)
            ->with('success', 'Credential created successfully.');
    }

    public function show(Credential $credential)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Log the view automatically using the enhanced logging
        $this->logViewAccess($credential);

        // Get recent access logs for this credential
        $recentLogs = CredentialAccessLog::where('credential_id', $credential->id)
            ->with(['user', 'otpRequest'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get access statistics for this credential
        $accessStats = [
            'total_views' => CredentialAccessLog::getAccessCountByAction($credential->id, 'view'),
            'total_copies' => CredentialAccessLog::where('credential_id', $credential->id)
                ->where('action', 'like', 'copy_%')
                ->count(),
            'unique_accessors' => CredentialAccessLog::getUniqueAccessors($credential->id),
            'last_accessed' => $credential->last_accessed_at,
        ];

        return view('credentials.show', compact('credential', 'recentLogs', 'accessStats'));
    }

    public function edit(Credential $credential)
    {
        $categories = CredentialCategory::active()->orderBy('name')->get();
        
        // Log edit page access
        CredentialAccessLog::logDetailedAccess($credential->id, 'edit_page_accessed', [
            'credential_name' => $credential->name,
            'action_type' => 'edit_form_view',
        ]);
        
        return view('credentials.edit', compact('credential', 'categories'));
    }

    public function update(Request $request, Credential $credential)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:credential_categories,id',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'website_url' => 'nullable|url|max:500',
            'backup_codes' => 'nullable|array',
            'backup_codes.*' => 'string|max:100',
            'expires_at' => 'nullable|date|after:today',
            'password_never_expires' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        $data = $validator->validated();
        
        // Store original data for change tracking
        $originalData = $credential->toArray();
        
        // Process backup codes
        if ($request->has('backup_codes')) {
            $data['backup_codes'] = array_filter($request->backup_codes, function ($code) {
                return !empty(trim($code));
            });
        }

        $this->credentialService->updateCredential($credential, $data, $user);

        // Log the update with changes
        $changes = [];
        foreach ($data as $key => $value) {
            if ($key !== 'password' && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value
                ];
            }
        }

        CredentialAccessLog::logDetailedAccess($credential->id, 'edit', [
            'credential_name' => $credential->name,
            'action_type' => 'update',
            'fields_changed' => array_keys($changes),
            'change_count' => count($changes),
        ]);

        return redirect()->route('credentials.show', $credential)
            ->with('success', 'Credential updated successfully.');
    }

    public function destroy(Credential $credential)
    {
        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        // Log deletion before deleting
        CredentialAccessLog::logDetailedAccess($credential->id, 'delete', [
            'credential_name' => $credential->name,
            'credential_category' => $credential->category->name ?? 'Uncategorized',
            'action_type' => 'deletion',
            'permanent_action' => true,
        ]);

        $this->credentialService->deleteCredential($credential, $user);

        return redirect()->route('credentials.index')
            ->with('success', 'Credential deleted successfully.');
    }

    /**
     * Enhanced copy method with detailed logging
     */
    public function copy(Request $request, Credential $credential)
    {
        $field = $request->input('field');
        $allowedFields = ['username', 'email', 'password'];

        if (!in_array($field, $allowedFields)) {
            abort(400, 'Invalid field specified.');
        }

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        // Log the copy action with enhanced details
        $this->logCopyAccess($credential, $field, $request);

        $value = $credential->{$field};

        return response()->json([
            'success' => true,
            'value' => $value,
            'message' => ucfirst($field) . ' copied to clipboard.',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * New method for enhanced access logging
     */
    public function logAccess(Request $request, Credential $credential)
    {
        try {
            $action = $request->input('action', 'view');
            
            // Use the enhanced logging method from CredentialAccessLog
            CredentialAccessLog::logDetailedAccess($credential->id, $action, [
                'request_data' => $request->only(['action', 'timestamp', 'user_agent']),
                'credential_name' => $credential->name,
                'credential_category' => $credential->category->name ?? 'Uncategorized',
            ]);

            // Update credential access statistics
            $credential->increment('access_count');
            $credential->update(['last_accessed_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Access logged successfully',
                'action' => $action,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log credential access: ' . $e->getMessage(), [
                'credential_id' => $credential->id,
                'user_id' => Auth::id() ?? 'guest',
                'action' => $request->input('action', 'unknown'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to log access',
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $categoryId = $request->input('category_id');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'credentials' => [],
                'message' => 'Please enter at least 2 characters to search.'
            ]);
        }

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $credentials = $this->credentialService->searchCredentials($query, $user, $categoryId);

        // Log search activity
        CredentialAccessLog::logDetailedAccess(null, 'search', [
            'search_query' => $query,
            'category_filter' => $categoryId,
            'results_count' => $credentials->total(),
            'action_type' => 'search_operation',
        ]);

        return response()->json([
            'credentials' => $credentials->items(),
            'pagination' => [
                'current_page' => $credentials->currentPage(),
                'last_page' => $credentials->lastPage(),
                'total' => $credentials->total(),
            ]
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Only administrators can export credentials.');
        }

        $credentials = Credential::with(['category', 'creator'])
            ->active()
            ->when($request->category_id, function ($query) use ($request) {
                return $query->byCategory($request->category_id);
            })
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        // Log export activity
        CredentialAccessLog::logDetailedAccess(null, 'bulk_export', [
            'export_count' => $credentials->count(),
            'category_filter' => $request->category_id,
            'action_type' => 'bulk_export',
            'admin_action' => true,
        ]);

        $csvData = [];
        $csvData[] = ['Category', 'Name', 'Username', 'Email', 'Website URL', 'Notes', 'Expires At', 'Created By', 'Created At'];

        foreach ($credentials as $credential) {
            $csvData[] = [
                $credential->category->name,
                $credential->name,
                $credential->username,
                $credential->email,
                $credential->website_url,
                $credential->notes,
                $credential->expires_at ? $credential->expires_at->format('Y-m-d') : 'Never',
                $credential->creator->name,
                $credential->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $filename = 'credentials-export-' . now()->format('Y-m-d-H-i-s') . '.csv';

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
     * Get access analytics for a specific credential
     */
    public function getAccessAnalytics(Credential $credential, Request $request)
    {
        try {
            $period = $request->input('period', 30);
            
            $analytics = [
                'basic_stats' => [
                    'total_accesses' => CredentialAccessLog::byCredential($credential->id)->count(),
                    'recent_accesses' => CredentialAccessLog::byCredential($credential->id)->recent($period)->count(),
                    'unique_users' => CredentialAccessLog::getUniqueAccessors($credential->id),
                    'action_breakdown' => CredentialAccessLog::byCredential($credential->id)
                        ->recent($period)
                        ->selectRaw('action, COUNT(*) as count')
                        ->groupBy('action')
                        ->pluck('count', 'action'),
                ],
                'security_info' => [
                    'after_hours_access' => CredentialAccessLog::byCredential($credential->id)
                        ->recent($period)
                        ->get()
                        ->filter(fn($log) => $log->isAfterHours())
                        ->count(),
                    'suspicious_activity' => CredentialAccessLog::byCredential($credential->id)
                        ->recent($period)
                        ->suspicious()
                        ->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'analytics' => $analytics,
                'period_days' => $period,
                'generated_at' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get access analytics: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Log bulk operations
     */
    public function logBulkOperation(Request $request)
    {
        try {
            $operation = $request->input('operation');
            $credentialIds = $request->input('credential_ids', []);
            
            foreach ($credentialIds as $credentialId) {
                CredentialAccessLog::logDetailedAccess($credentialId, 'bulk_' . $operation, [
                    'bulk_operation' => true,
                    'operation_type' => $operation,
                    'total_credentials' => count($credentialIds),
                    'batch_timestamp' => now()->toISOString(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk operation logged successfully',
                'credentials_count' => count($credentialIds),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log bulk operation: ' . $e->getMessage(), [
                'operation' => $request->input('operation', 'unknown'),
                'credential_count' => count($request->input('credential_ids', [])),
                'user_id' => Auth::id() ?? 'guest',
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Private method to log view access
     */
    private function logViewAccess(Credential $credential)
    {
        try {
            CredentialAccessLog::logDetailedAccess($credential->id, 'view', [
                'credential_name' => $credential->name,
                'credential_category' => $credential->category->name ?? 'Uncategorized',
                'auto_logged' => true,
                'view_type' => 'credential_detail_page',
            ]);

            // Update credential stats
            $credential->increment('access_count');
            $credential->update(['last_accessed_at' => now()]);

        } catch (\Exception $e) {
            Log::error('Failed to log credential view: ' . $e->getMessage(), [
                'credential_id' => $credential->id,
                'user_id' => Auth::id() ?? 'guest',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Private method to log copy access
     */
    private function logCopyAccess(Credential $credential, $field, $request = null)
    {
        try {
            $action = 'copy_' . $field;
            $request = $request ?? request();
            
            // Determine what was actually copied (without logging the actual value)
            $fieldInfo = [
                'field_type' => $field,
                'credential_name' => $credential->name,
                'credential_category' => $credential->category->name ?? 'Uncategorized',
                'copy_method' => 'manual_click',
            ];

            // Add field-specific metadata
            switch($field) {
                case 'password':
                    $fieldInfo['field_length'] = strlen($credential->password ?? '');
                    $fieldInfo['has_special_chars'] = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $credential->password ?? '') ? true : false;
                    break;
                case 'username':
                    $fieldInfo['field_length'] = strlen($credential->username ?? '');
                    $fieldInfo['is_email_format'] = filter_var($credential->username, FILTER_VALIDATE_EMAIL) ? true : false;
                    break;
                case 'email':
                    $fieldInfo['field_length'] = strlen($credential->email ?? '');
                    $fieldInfo['email_domain'] = $credential->email ? substr(strrchr($credential->email, "@"), 1) : null;
                    break;
            }

            CredentialAccessLog::logDetailedAccess($credential->id, $action, $fieldInfo);

        } catch (\Exception $e) {
            Log::error('Failed to log credential copy: ' . $e->getMessage(), [
                'credential_id' => $credential->id,
                'field' => $field,
                'user_id' => Auth::id() ?? 'guest',
                'error' => $e->getMessage(),
            ]);
        }
    }
}