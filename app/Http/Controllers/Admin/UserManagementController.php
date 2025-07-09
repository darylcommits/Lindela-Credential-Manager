<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Exception;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.active', 'admin', 'track.activity']);
    }

    public function index(Request $request)
    {
        try {
            $users = User::when($request->search, function ($query) use ($request) {
                    return $query->where(function ($q) use ($request) {
                        $q->where('name', 'like', "%{$request->search}%")
                          ->orWhere('email', 'like', "%{$request->search}%");
                    });
                })
                ->when($request->role, function ($query) use ($request) {
                    return $query->where('role', $request->role);
                })
                ->when($request->status, function ($query) use ($request) {
                    $isActive = $request->status === 'active';
                    return $query->where('is_active', $isActive);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('admin.users.index', compact('users'));
            
        } catch (Exception $e) {
            Log::error('User index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load users. Please try again.');
        }
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,it_staff',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // FIXED: Proper checkbox handling
            $isActive = $request->has('is_active') && $request->is_active == '1';
            
            // Create user with auto-verification
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => $isActive,
                'email_verified_at' => now(), // Auto-verify admin-created users
            ]);

            // Verify user was created successfully
            if (!$user || !$user->id) {
                throw new Exception('User creation failed - no user object returned');
            }

            // Log the successful creation
            SystemLog::logAction(
                'user_created',
                'admin',
                "User '{$user->name}' ({$user->email}) created with role '{$user->role}' by admin '" . Auth::user()->name . "'",
                'info',
                Auth::id(),
                [
                    'created_user_id' => $user->id,
                    'user_role' => $user->role,
                    'user_active' => $user->is_active,
                    'auto_verified' => true,
                ]
            );

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$user->name}' created successfully! They can login immediately with their credentials.");

        } catch (Exception $e) {
            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password', 'password_confirmation']),
                'admin_user' => Auth::id()
            ]);

            SystemLog::logAction(
                'user_creation_failed',
                'admin',
                "Failed to create user with email '{$request->email}': " . $e->getMessage(),
                'error',
                Auth::id(),
                [
                    'error_message' => $e->getMessage(),
                    'request_email' => $request->email,
                    'request_name' => $request->name,
                    'request_role' => $request->role
                ]
            );

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        try {
            // Load relationships for the user detail view if they exist
            if (method_exists($user, 'createdCredentials')) {
                $user->load([
                    'createdCredentials' => function($query) {
                        $query->latest()->limit(5);
                    }
                ]);
            }
            
            if (method_exists($user, 'otpRequests')) {
                $user->load([
                    'otpRequests' => function ($query) {
                        $query->orderBy('created_at', 'desc')->limit(10);
                    }
                ]);
            }

            // Get recent activity from SystemLog
            $recentActivity = SystemLog::where('user_id', $user->id)
                ->orWhere(function($query) use ($user) {
                    $query->whereJsonContains('metadata->created_user_id', $user->id)
                          ->orWhereJsonContains('metadata->target_user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return view('admin.users.show', compact('user', 'recentActivity'));
            
        } catch (Exception $e) {
            Log::error('User show error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load user details.');
        }
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,it_staff',
            'is_active' => 'nullable|boolean',
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $currentUser = Auth::user();
            
            // FIXED: Proper checkbox handling
            $isActive = $request->has('is_active') && $request->is_active == '1';
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_active' => $isActive,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Special validation for self-editing
            if ($user->id === $currentUser->id) {
                // Prevent admin from demoting themselves
                if ($user->role === 'admin' && $updateData['role'] !== 'admin') {
                    return back()->with('error', 'You cannot change your own role from admin to a lower privilege.');
                }
                
                // Prevent admin from deactivating themselves
                if ($user->is_active && !$updateData['is_active']) {
                    return back()->with('error', 'You cannot deactivate your own account.');
                }
            }

            // Prevent deactivating the last admin
            if ($user->role === 'admin' && $user->is_active && !$updateData['is_active']) {
                $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
                if ($adminCount <= 1) {
                    return back()->with('error', 'Cannot deactivate the last active admin user.');
                }
            }

            // Update the user
            $user->update($updateData);

            // Log successful changes
            $changes = [];
            foreach (['name', 'email', 'role', 'is_active'] as $field) {
                if ($user->getOriginal($field) !== $updateData[$field]) {
                    $changes[$field] = [
                        'old' => $user->getOriginal($field),
                        'new' => $updateData[$field]
                    ];
                }
            }

            if ($request->filled('password')) {
                $changes['password'] = 'updated';
            }

            if (!empty($changes)) {
                SystemLog::logAction(
                    'user_updated',
                    'admin',
                    "User '{$user->name}' updated by admin '{$currentUser->name}'",
                    'info',
                    $currentUser->id,
                    [
                        'updated_user_id' => $user->id,
                        'changes' => $changes,
                        'is_self_edit' => $user->id === $currentUser->id,
                    ]
                );
            }

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User updated successfully.');

        } catch (Exception $e) {
            Log::error('User update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_user' => Auth::id(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser || $user->id === $currentUser->id) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            if ($user->role === 'admin') {
                $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
                if ($adminCount <= 1) {
                    return back()->with('error', 'Cannot delete the last active admin user.');
                }
            }

            $userName = $user->name;
            $userEmail = $user->email;
            $userRole = $user->role;

            // Log before deletion
            SystemLog::logAction(
                'user_deleted',
                'admin',
                "User '{$userName}' ({$userEmail}) with role '{$userRole}' deleted by admin '{$currentUser->name}'",
                'warning',
                $currentUser->id,
                [
                    'deleted_user_id' => $user->id,
                    'deleted_user_name' => $userName,
                    'deleted_user_email' => $userEmail,
                    'deleted_user_role' => $userRole
                ]
            );

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$userName}' deleted successfully.");

        } catch (Exception $e) {
            Log::error('User deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_user' => Auth::id()
            ]);

            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    // FIXED: Updated toggleStatus method to match frontend expectations
    public function toggleStatus(Request $request, User $user)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated.'
                ], 401);
            }
            
            if ($user->id === $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'You cannot modify your own account status.'
                ], 400);
            }

            // Validate the request - frontend sends is_active boolean
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $newStatus = $request->is_active;
            
            // Check admin count only when DEACTIVATING an admin
            if ($user->role === 'admin' && $user->is_active && !$newStatus) {
                $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Cannot deactivate the last active admin user.'
                    ], 400);
                }
            }

            // Update the user status
            $user->update(['is_active' => $newStatus]);
            
            $action = $newStatus ? 'activated' : 'deactivated';
            
            // Log the action
            SystemLog::logAction(
                'user_status_changed',
                'admin',
                "User '{$user->name}' {$action} by admin '{$currentUser->name}'",
                'info',
                $currentUser->id,
                [
                    'target_user_id' => $user->id,
                    'old_status' => !$newStatus,
                    'new_status' => $newStatus,
                    'action' => $action,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "User '{$user->name}' {$action} successfully.",
                'is_active' => $user->is_active,
                'status_text' => $user->is_active ? 'Active' : 'Inactive'
            ]);

        } catch (Exception $e) {
            Log::error('User status toggle failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_user' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update user status. Please try again.'
            ], 500);
        }
    }

    /**
     * Get user statistics for dashboard or reports
     */
    public function getUserStats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'admin_users' => User::where('role', 'admin')->where('is_active', true)->count(),
                'it_staff_users' => User::where('role', 'it_staff')->where('is_active', true)->count(),
                'recently_created' => User::where('created_at', '>=', now()->subDays(7))->count(),
                'last_login_users' => User::whereNotNull('last_login_at')
                    ->where('last_login_at', '>=', now()->subDays(30))->count(),
                'verified_users' => User::whereNotNull('email_verified_at')->count(),
            ];

            return response()->json($stats);

        } catch (Exception $e) {
            Log::error('User stats error: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch user statistics'], 500);
        }
    }

    /**
     * Bulk operations for future expansion
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $currentUser = Auth::user();
            $userIds = $request->user_ids;
            
            // Remove current user from bulk operations
            $userIds = array_filter($userIds, fn($id) => $id != $currentUser->id);
            
            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'No valid users selected for bulk operation.'
                ], 400);
            }

            $users = User::whereIn('id', $userIds)->get();
            $processed = 0;

            foreach ($users as $user) {
                $shouldSkip = false;
                
                // Check if we should skip this user for admin-related operations
                if (in_array($request->action, ['deactivate', 'delete']) && $user->role === 'admin') {
                    $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
                    if ($adminCount <= 1) {
                        $shouldSkip = true;
                    }
                }
                
                if ($shouldSkip) {
                    continue; // Skip this user and move to next
                }
                
                switch ($request->action) {
                    case 'activate':
                        if (!$user->is_active) {
                            $user->is_active = true;
                            $user->save();
                            $processed++;
                        }
                        break;
                        
                    case 'deactivate':
                        if ($user->is_active) {
                            $user->is_active = false;
                            $user->save();
                            $processed++;
                        }
                        break;
                        
                    case 'delete':
                        $user->delete();
                        $processed++;
                        break;
                }
            }

            // Log bulk operation
            SystemLog::logAction(
                'bulk_user_operation',
                'admin',
                "Bulk {$request->action} operation performed on {$processed} users by admin '{$currentUser->name}'",
                'info',
                $currentUser->id,
                [
                    'action' => $request->action,
                    'user_ids' => $userIds,
                    'processed_count' => $processed
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Bulk operation completed. {$processed} users processed.",
                'processed_count' => $processed
            ]);

        } catch (Exception $e) {
            Log::error('Bulk user operation failed', [
                'action' => $request->action,
                'user_ids' => $request->user_ids ?? [],
                'error' => $e->getMessage(),
                'admin_user' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Bulk operation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug methods for development
     */
    public function debugUser(User $user = null)
    {
        if (!config('app.debug')) {
            abort(404);
        }

        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403);
        }

        $user = $user ?? $currentUser;

        // Safely get user data
        $userData = $user->getAttributes();

        return response()->json([
            'user_data' => $userData,
            'relationships' => [
                'created_credentials_count' => method_exists($user, 'createdCredentials') ? $user->createdCredentials()->count() : 0,
                'otp_requests_count' => method_exists($user, 'otpRequests') ? $user->otpRequests()->count() : 0,
                'approved_otp_requests_count' => method_exists($user, 'approvedOtpRequests') ? $user->approvedOtpRequests()->count() : 0,
                'credential_access_logs_count' => method_exists($user, 'credentialAccessLogs') ? $user->credentialAccessLogs()->count() : 0,
            ],
            'permissions' => [
                'can_login' => method_exists($user, 'canLogin') ? $user->canLogin() : ($user->is_active && !is_null($user->email_verified_at)),
                'can_manage_users' => method_exists($user, 'canManageUsers') ? $user->canManageUsers() : ($user->role === 'admin'),
                'can_manage_otp' => method_exists($user, 'canManageOtp') ? $user->canManageOtp() : ($user->role === 'admin'),
                'can_access_credentials' => method_exists($user, 'canAccessCredentials') ? $user->canAccessCredentials() : true,
                'has_valid_otp' => method_exists($user, 'hasValidOtp') ? $user->hasValidOtp() : false,
                'is_admin' => method_exists($user, 'isAdmin') ? $user->isAdmin() : ($user->role === 'admin'),
            ],
            'session_info' => [
                'otp_session_expires_at' => $user->otp_session_expires_at,
                'last_login_at' => $user->last_login_at,
                'last_activity_at' => $user->last_activity_at,
            ]
        ]);
    }

    public function testCreate()
    {
        if (!config('app.debug')) {
            abort(404);
        }

        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            abort(403);
        }

        try {
            $testUser = User::create([
                'name' => 'Test User ' . now()->timestamp,
                'email' => 'test.' . now()->timestamp . '@example.com',
                'password' => Hash::make('password123'),
                'role' => 'it_staff',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test user created successfully',
                'user' => $testUser->getAttributes(),
                'can_login' => method_exists($testUser, 'canLogin') ? $testUser->canLogin() : true,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}