{{-- File: resources/views/admin/users/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ $user->name }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('User Details & Activity') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>{{ __('Edit User') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gradient-to-r from-gray-600 to-gray-500 text-white hover:from-gray-500 hover:to-gray-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>{{ __('Back to Users') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

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

        .user-avatar {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
            font-size: 2rem;
            margin: 0 auto;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (min-width: 1024px) {
            .info-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        .info-item {
            text-align: center;
            padding: 24px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.2);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            border-color: rgba(255, 221, 0, 0.4);
            background: rgba(255, 221, 0, 0.05);
            transform: translateY(-2px);
        }

        .info-label {
            color: #fbbf24;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .info-value {
            color: #ffffff;
            font-size: 1.125rem;
            font-weight: 500;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .role-badge {
            padding: 6px 16px;
            border-radius: 12px;
            font-size: 0.875rem;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.2);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: rgba(255, 221, 0, 0.4);
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #fbbf24;
            display: block;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            margin-top: 4px;
        }

        .activity-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 221, 0, 0.1);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255, 221, 0, 0.05);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon.info {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .activity-icon.warning {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .activity-icon.success {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .activity-icon.error {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .activity-content {
            flex: 1;
            margin-left: 12px;
        }

        .activity-title {
            color: #ffffff;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .activity-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
            margin-bottom: 4px;
        }

        .activity-time {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.75rem;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: rgba(255, 255, 255, 0.6);
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            opacity: 0.5;
        }

        .otp-item {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 8px;
        }

        .otp-status {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .otp-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .otp-approved {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .otp-used {
            background: rgba(107, 114, 128, 0.2);
            color: #9ca3af;
        }

        .otp-expired {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .tab-container {
            margin-top: 32px;
        }

        .tab-buttons {
            display: flex;
            border-bottom: 2px solid rgba(255, 221, 0, 0.2);
            margin-bottom: 24px;
        }

        .tab-button {
            padding: 12px 24px;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .tab-button.active {
            color: #fbbf24;
            border-bottom-color: #fbbf24;
        }

        .tab-button:hover {
            color: #ffffff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- User Profile Card -->
            <div class="glass-card rounded-2xl p-8 mb-8 animate-fade-in">
                <div class="text-center mb-8">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-2xl font-bold text-white mt-4 mb-2">{{ $user->name }}</h3>
                    <p class="text-gray-400 mb-4">{{ $user->email }}</p>
                    <div class="flex justify-center items-center space-x-4">
                        <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="4"/>
                            </svg>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="role-badge {{ $user->role === 'admin' ? 'role-admin' : 'role-it-staff' }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="status-badge" style="background: rgba(34, 197, 94, 0.2); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verified
                            </span>
                        @else
                            <span class="status-badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Unverified
                            </span>
                        @endif
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Member Since</div>
                        <div class="info-value">{{ $user->created_at->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->format('M d, Y') }}
                                <div class="text-sm text-gray-400 mt-1">{{ $user->last_login_at->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-500">Never</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Activity</div>
                        <div class="info-value">
                            @if($user->last_activity_at)
                                {{ $user->last_activity_at->format('M d, Y') }}
                                <div class="text-sm text-gray-400 mt-1">{{ $user->last_activity_at->diffForHumans() }}</div>
                            @else
                                <span class="text-gray-500">No activity</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="glass-card rounded-2xl p-8 mb-8 animate-fade-in">
                <h3 class="text-xl font-bold text-white mb-6">User Statistics</h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-number">{{ $user->createdCredentials->count() }}</span>
                        <div class="stat-label">Credentials Created</div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $user->otpRequests->count() }}</span>
                        <div class="stat-label">OTP Requests</div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $user->approvedOtpRequests->count() }}</span>
                        <div class="stat-label">OTP Approvals</div>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $user->credentialAccessLogs->count() }}</span>
                        <div class="stat-label">Credential Access</div>
                    </div>
                </div>
            </div>

            <!-- Tabs Container -->
            <div class="glass-card rounded-2xl p-8 animate-fade-in">
                <div class="tab-container">
                    <div class="tab-buttons">
                        <button class="tab-button active" onclick="switchTab('recent-activity')">Recent Activity</button>
                        @if($user->role === 'it_staff')
                            <button class="tab-button" onclick="switchTab('otp-requests')">OTP Requests</button>
                        @endif
                        <button class="tab-button" onclick="switchTab('system-info')">System Information</button>
                    </div>

                    <!-- Recent Activity Tab -->
                    <div id="recent-activity" class="tab-content active">
                        <h4 class="text-lg font-bold text-white mb-4">Recent Activity</h4>
                        @if($recentActivity && $recentActivity->count() > 0)
                            <div class="activity-list">
                                @foreach($recentActivity as $activity)
                                    <div class="activity-item flex items-start">
                                        <div class="activity-icon {{ $activity->level }}">
                                            @switch($activity->level)
                                                @case('info')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    @break
                                                @case('warning')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    @break
                                                @case('success')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    @break
                                                @case('error')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    @break
                                                @default
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                            @endswitch
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</div>
                                            <div class="activity-description">{{ $activity->description }}</div>
                                            <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p>No recent activity found for this user.</p>
                            </div>
                        @endif
                    </div>

                    <!-- OTP Requests Tab -->
                    @if($user->role === 'it_staff')
                        <div id="otp-requests" class="tab-content">
                            <h4 class="text-lg font-bold text-white mb-4">OTP Requests History</h4>
                            @if($user->otpRequests && $user->otpRequests->count() > 0)
                                <div class="space-y-3">
                                    @foreach($user->otpRequests as $otpRequest)
                                        <div class="otp-item">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-white">
                                                        Reason: {{ $otpRequest->reason ?? 'No reason provided' }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        Requested: {{ $otpRequest->created_at->format('M d, Y H:i') }}
                                                    </div>
                                                    @if($otpRequest->approved_at)
                                                        <div class="text-xs text-green-400">
                                                            Approved: {{ $otpRequest->approved_at->format('M d, Y H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($otpRequest->used_at)
                                                        <div class="text-xs text-gray-400">
                                                            Used: {{ $otpRequest->used_at->format('M d, Y H:i') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col items-end gap-2">
                                                    <span class="otp-status otp-{{ $otpRequest->status }}">
                                                        {{ ucfirst($otpRequest->status) }}
                                                    </span>
                                                    @if($otpRequest->expires_at)
                                                        <div class="text-xs text-gray-400">
                                                            @if($otpRequest->expires_at->isPast())
                                                                Expired {{ $otpRequest->expires_at->diffForHumans() }}
                                                            @else
                                                                Expires {{ $otpRequest->expires_at->diffForHumans() }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <p>No OTP requests found for this user.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- System Information Tab -->
                    <div id="system-info" class="tab-content">
                        <h4 class="text-lg font-bold text-white mb-4">System Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-md font-semibold text-yellow-400 mb-3">Account Details</h5>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">User ID:</span>
                                        <span class="text-white">{{ $user->id }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Email Verified:</span>
                                        <span class="text-white">
                                            @if($user->email_verified_at)
                                                Yes ({{ $user->email_verified_at->format('M d, Y') }})
                                            @else
                                                No
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Two-Factor Enabled:</span>
                                        <span class="text-white">
                                            {{ $user->two_factor_enabled ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Account Created:</span>
                                        <span class="text-white">{{ $user->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Last Updated:</span>
                                        <span class="text-white">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-md font-semibold text-yellow-400 mb-3">Access Information</h5>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Can Manage Users:</span>
                                        <span class="text-white">{{ $user->canManageUsers() ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Can Manage OTP:</span>
                                        <span class="text-white">{{ $user->canManageOtp() ? 'Yes' : 'No' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Can Access Credentials:</span>
                                        <span class="text-white">{{ $user->canAccessCredentials() ? 'Yes' : 'No' }}</span>
                                    </div>
                                    @if($user->role === 'it_staff')
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Has Valid OTP:</span>
                                            <span class="text-white">{{ $user->hasValidOtp() ? 'Yes' : 'No' }}</span>
                                        </div>
                                        @if($user->otp_session_expires_at)
                                            <div class="flex justify-between">
                                                <span class="text-gray-400">OTP Session Expires:</span>
                                                <span class="text-white">{{ $user->otp_session_expires_at->diffForHumans() }}</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }

            // Add active class to clicked button
            event.target.classList.add('active');
        }

        // Auto-refresh activity data every 30 seconds
        setInterval(function() {
            const activeTab = document.querySelector('.tab-content.active');
            if (activeTab && activeTab.id === 'recent-activity') {
                // Only refresh if we're on the activity tab
                // This would require an AJAX endpoint to fetch new activity
                console.log('Auto-refresh activity data');
            }
        }, 30000);

        // Keyboard navigation for tabs
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                const tabButtons = document.querySelectorAll('.tab-button');
                const activeIndex = Array.from(tabButtons).findIndex(btn => btn.classList.contains('active'));
                
                if (e.key === 'ArrowLeft' && activeIndex > 0) {
                    e.preventDefault();
                    tabButtons[activeIndex - 1].click();
                } else if (e.key === 'ArrowRight' && activeIndex < tabButtons.length - 1) {
                    e.preventDefault();
                    tabButtons[activeIndex + 1].click();
                }
            }
        });
    </script>
</x-app-layout>