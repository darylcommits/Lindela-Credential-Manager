{{-- File: resources/views/admin/users/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('User Management') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Manage system users and permissions') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="stats-info">
                    Total: {{ $users->total() }} users
                </div>
                <a href="{{ route('admin.users.create') }}" 
                   class="bg-gradient-to-r from-yellow-600 to-yellow-500 text-black hover:from-yellow-500 hover:to-yellow-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>{{ __('Add User') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- FIXED: Add CSRF token meta tag for proper AJAX authentication -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom Styles -->
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-card {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(255, 221, 0, 0.25);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
        }
        
        .dashboard-container {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 30%, #2a2418 50%, #1a1a1a 70%, #000000 100%);
            min-height: 100vh;
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out;
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes slideUp {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .form-input {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #ffdd00;
            background: rgba(255, 221, 0, 0.1);
            box-shadow: 0 0 15px rgba(255, 221, 0, 0.2);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ffdd00, #f59e0b);
            color: #000;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            transform: scale(1.05);
        }

        .btn-secondary {
            background: rgba(75, 85, 99, 0.8);
            color: #ffffff;
            border: 1px solid rgba(255, 221, 0, 0.3);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
            transform: scale(1.05);
        }

        .btn-clear {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
        }

        .btn-clear:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: #ef4444;
            transform: scale(1.05);
        }

        .user-table {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.2);
        }

        .table-header {
            background: rgba(0, 0, 0, 0.6);
            border-bottom: 1px solid rgba(255, 221, 0, 0.3);
        }

        .table-header th {
            color: #ffdd00;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 24px;
        }

        .table-row {
            border-bottom: 1px solid rgba(255, 221, 0, 0.1);
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background: rgba(255, 221, 0, 0.05);
            border-color: rgba(255, 221, 0, 0.3);
        }

        .table-cell {
            padding: 16px 24px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* FIXED: Consistent user avatar styling with edit/create forms */
        .user-avatar {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
            font-size: 16px;
        }

        .role-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-admin {
            background: rgba(147, 51, 234, 0.2);
            color: #a855f7;
            border: 1px solid rgba(147, 51, 234, 0.3);
        }

        .role-it-staff {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* FIXED: Enhanced status badge styling with consistent design */
        .status-badge {
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 80px;
            justify-content: center;
        }

        .status-badge:hover:not(.status-badge-disabled) {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .status-badge.loading {
            opacity: 0.7;
            cursor: not-allowed;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .status-badge-disabled {
            cursor: not-allowed;
            opacity: 0.8;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-active:hover:not(.status-badge-disabled) {
            background: rgba(34, 197, 94, 0.3);
            border-color: #22c55e;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-inactive:hover:not(.status-badge-disabled) {
            background: rgba(239, 68, 68, 0.3);
            border-color: #ef4444;
        }

        .action-link {
            color: #60a5fa;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 4px 8px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .action-link:hover {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            transform: scale(1.05);
        }

        .action-delete {
            color: #f87171;
        }

        .action-delete:hover {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        .empty-state {
            background: rgba(255, 221, 0, 0.05);
            border: 2px dashed rgba(255, 221, 0, 0.3);
            border-radius: 16px;
            padding: 48px;
        }

        .stats-info {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            animation: slideDown 0.3s ease-out;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        @keyframes slideDown {
            0% { transform: translateY(-10px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border-radius: 6px;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a {
            background: rgba(75, 85, 99, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.3);
        }

        .pagination a:hover {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
        }

        .pagination .current {
            background: linear-gradient(135deg, #ffdd00, #f59e0b);
            color: #000;
            font-weight: 600;
        }

        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .verified-check-icon {
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            animation: verifiedPulse 2s ease-in-out infinite;
        }

        @keyframes verifiedPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }
            50% {
                box-shadow: 0 0 0 4px rgba(34, 197, 94, 0);
            }
        }

        /* Loading indicator for status updates */
        .status-loading {
            animation: statusSpin 1s linear infinite;
        }

        @keyframes statusSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success mb-6">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error mb-6">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="glass-card rounded-2xl p-6 mb-8 animate-fade-in">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-yellow-400 mb-2">Search Users</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search users by name or email..." 
                               class="w-full rounded-xl px-4 py-3 form-input">
                    </div>
                    <div class="lg:w-48">
                        <label class="block text-sm font-medium text-yellow-400 mb-2">Role</label>
                        <select name="role" class="w-full rounded-xl px-4 py-3 form-input">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="it_staff" {{ request('role') === 'it_staff' ? 'selected' : '' }}>IT Staff</option>
                        </select>
                    </div>
                    <div class="lg:w-48">
                        <label class="block text-sm font-medium text-yellow-400 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl px-4 py-3 form-input">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex flex-col justify-end">
                        <div class="flex gap-3">
                            <button type="submit" class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 btn-secondary flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span>Search</span>
                            </button>
                            @if(request('search') || request('role') || request('status'))
                                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 btn-clear flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span>Clear</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users List -->
            <div class="glass-card rounded-2xl overflow-hidden animate-fade-in">
                @if($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full user-table">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-left">User</th>
                                    <th class="text-left">Role</th>
                                    <th class="text-left">Status</th>
                                    <th class="text-left">Last Login</th>
                                    <th class="text-left">Created</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr class="table-row" id="user-row-{{ $user->id }}">
                                        <td class="table-cell">
                                            <div class="flex items-center space-x-4">
                                                <div class="user-avatar">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-white">{{ $user->name }}</div>
                                                    <div class="text-sm stats-info">{{ $user->email }}</div>
                                                    @if($user->email_verified_at)
                                                        <div class="text-xs text-green-400 flex items-center space-x-1 mt-1">
                                                            <div class="verified-check-icon">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
                                                            <span>Verified</span>
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-yellow-400 flex items-center space-x-1 mt-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span>Unverified</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table-cell">
                                            <span class="role-badge {{ $user->role === 'admin' ? 'role-admin' : 'role-it-staff' }}">
                                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                            </span>
                                        </td>
                                        <td class="table-cell">
                                            @if($user->id !== auth()->id())
                                                <button onclick="toggleUserStatus({{ $user->id }})"
                                                        class="status-badge status-toggle-{{ $user->id }} {{ $user->is_active ? 'status-active' : 'status-inactive' }}"
                                                        data-user-id="{{ $user->id }}"
                                                        data-current-status="{{ $user->is_active ? 'true' : 'false' }}"
                                                        title="Click to {{ $user->is_active ? 'deactivate' : 'activate' }} user">
                                                    <span class="status-icon">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <circle cx="10" cy="10" r="4"/>
                                                        </svg>
                                                    </span>
                                                    <span class="status-text">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                                </button>
                                            @else
                                                <span class="status-badge status-badge-disabled {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                                    <span class="status-icon">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <circle cx="10" cy="10" r="4"/>
                                                        </svg>
                                                    </span>
                                                    <span class="status-text">{{ $user->is_active ? 'Active' : 'Inactive' }} (You)</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="table-cell stats-info">
                                            @if($user->last_login_at)
                                                <div class="text-sm">{{ $user->last_login_at->diffForHumans() }}</div>
                                                <div class="text-xs opacity-70">{{ $user->last_login_at->format('M d, Y H:i') }}</div>
                                            @else
                                                <span class="text-gray-500">Never</span>
                                            @endif
                                        </td>
                                        <td class="table-cell stats-info">
                                            <div class="text-sm">{{ $user->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs opacity-70">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="table-cell text-right">
                                            <div class="flex justify-end space-x-3">
                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                   class="action-link flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <span>View</span>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                   class="action-link flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button" 
                                                            onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->name }}')"
                                                            class="action-link action-delete flex items-center space-x-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        <span>Delete</span>
                                                    </button>
                                                    <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-6 border-t border-yellow-400/20">
                        <div class="flex items-center justify-between">
                            <div class="stats-info">
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                            </div>
                            <div class="pagination">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="empty-state mx-auto max-w-md">
                            <svg class="mx-auto h-16 w-16 text-yellow-400/50 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-white mb-4">No users found</h3>
                            <p class="text-gray-400 mb-8">
                                @if(request('search') || request('role') || request('status'))
                                    Try adjusting your search criteria or filters.
                                @else
                                    Get started by adding a new user.
                                @endif
                            </p>
                            <div class="flex justify-center gap-3">
                                @if(request('search') || request('role') || request('status'))
                                    <a href="{{ route('admin.users.index') }}" 
                                       class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 btn-clear flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span>Clear Filters</span>
                                    </a>
                                @else
                                    <a href="{{ route('admin.users.create') }}" 
                                       class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 btn-primary flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Add User</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- JavaScript for Enhanced Functionality -->
    <script>
        // FIXED: Improved CSRF token handling
        function getCSRFToken() {
            // Try meta tag first (most reliable)
            let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) return token;
            
            // Fallback to hidden input if exists
            token = document.querySelector('input[name="_token"]')?.value;
            if (token) return token;
            
            console.error('CSRF token not found. Please ensure the meta tag is present.');
            return null;
        }

        // FIXED: Enhanced user status toggle with proper route and messaging
        async function toggleUserStatus(userId) {
            const button = document.querySelector(`.status-toggle-${userId}`);
            const statusText = button.querySelector('.status-text');
            const statusIcon = button.querySelector('.status-icon svg');
            const row = document.getElementById(`user-row-${userId}`);
            const csrfToken = getCSRFToken();
            
            if (!button) {
                showAlert('Error: User status button not found', 'error');
                return;
            }

            if (!csrfToken) {
                showAlert('Error: CSRF token not found. Please refresh the page and try again.', 'error');
                return;
            }

            // Prevent double-clicking
            if (button.classList.contains('loading')) {
                return;
            }

            // Get current status
            const currentStatus = button.getAttribute('data-current-status') === 'true';
            const newStatus = !currentStatus;
            
            // Confirm action for deactivation
            if (currentStatus) {
                const confirmed = confirm(`Are you sure you want to deactivate this user?\n\nThey will not be able to log in until reactivated.`);
                if (!confirmed) {
                    return;
                }
            }

            // Add loading state
            button.classList.add('loading');
            button.disabled = true;
            const originalText = statusText.textContent;
            statusText.textContent = 'Updating...';
            statusIcon.classList.add('status-loading');
            
            try {
                // FIXED: Use correct endpoint for status toggle
                const response = await fetch(`/admin/users/${userId}/toggle-status`, {
                    method: 'PATCH', // Use PATCH instead of POST for better semantics
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        is_active: newStatus
                    })
                });

                // Check if response is valid
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error:', response.status, errorText);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Update button appearance with animation
                    const newStatusText = data.is_active ? 'Active' : 'Inactive';
                    statusText.textContent = newStatusText;
                    button.className = `status-badge status-toggle-${userId} ${data.is_active ? 'status-active' : 'status-inactive'}`;
                    button.setAttribute('data-current-status', data.is_active ? 'true' : 'false');
                    button.title = `Click to ${data.is_active ? 'deactivate' : 'activate'} user`;
                    
                    // FIXED: Show proper success message
                    const actionText = data.is_active ? 'activated' : 'deactivated';
                    showAlert(`User successfully ${actionText}`, 'success');
                    
                    // Add visual feedback with animation
                    if (row) {
                        row.style.background = 'rgba(34, 197, 94, 0.1)';
                        row.style.transform = 'scale(1.02)';
                        setTimeout(() => {
                            row.style.background = '';
                            row.style.transform = '';
                        }, 2000);
                    }
                } else {
                    throw new Error(data.message || data.error || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Toggle status error:', error);
                let errorMessage = 'An error occurred while updating user status';
                
                if (error.message.includes('HTTP 401')) {
                    errorMessage = 'Session expired. Please refresh the page and log in again.';
                } else if (error.message.includes('HTTP 403')) {
                    errorMessage = 'You do not have permission to perform this action.';
                } else if (error.message.includes('HTTP 404')) {
                    errorMessage = 'User not found or route not configured. Please refresh the page and try again.';
                } else if (error.message.includes('HTTP 419')) {
                    errorMessage = 'Session expired. Please refresh the page and try again.';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Network error. Please check your connection and try again.';
                } else if (error.message !== 'An error occurred while updating user status') {
                    errorMessage = error.message;
                }
                
                showAlert(errorMessage, 'error');
                statusText.textContent = originalText;
            } finally {
                // Remove loading state
                button.classList.remove('loading');
                button.disabled = false;
                statusIcon.classList.remove('status-loading');
            }
        }

        // Confirm Delete User
        function confirmDeleteUser(userId, userName) {
            const isConfirmed = confirm(`Are you sure you want to DELETE user "${userName}"?\n\nThis action cannot be undone and will permanently remove:\n- The user account\n- All credentials they created\n- All their activity logs\n- All their OTP requests\n\nThis is different from deactivating the user.`);
            
            if (isConfirmed) {
                const doubleConfirm = prompt(`To confirm DELETION, please type "DELETE" (without quotes):`);
                if (doubleConfirm === 'DELETE') {
                    const form = document.getElementById(`delete-form-${userId}`);
                    if (form) {
                        form.submit();
                    } else {
                        showAlert('Error: Delete form not found', 'error');
                    }
                } else {
                    showAlert('Deletion cancelled. You must type "DELETE" exactly to confirm.', 'error');
                }
            }
        }

        // FIXED: Enhanced alert system with better positioning
        function showAlert(message, type = 'success') {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.dynamic-alert');
            existingAlerts.forEach(alert => alert.remove());

            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} dynamic-alert`;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.maxWidth = '400px';
            alertDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${type === 'success' 
                                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                            }
                        </svg>
                        <span>${message}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-current opacity-70 hover:opacity-100 ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;

            // Add to body instead of container for better positioning
            document.body.appendChild(alertDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Auto-hide flash messages
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.dynamic-alert)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        setTimeout(() => alert.remove(), 300);
                    }
                }, 5000);
            });
        });

        // Enhanced search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const form = searchInput?.closest('form');
            
            if (searchInput && form) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (this.value.length >= 3 || this.value.length === 0) {
                            form.submit();
                        }
                    }, 500);
                });
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K for search focus
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            
            // Ctrl/Cmd + N for new user
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                window.location.href = "{{ route('admin.users.create') }}";
            }
        });

        // Connection status monitoring
        window.addEventListener('online', function() {
            showAlert('Connection restored', 'success');
        });

        window.addEventListener('offline', function() {
            showAlert('Connection lost. Please check your internet connection.', 'error');
        });
    </script>
</x-app-layout>