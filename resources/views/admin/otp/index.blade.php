{{-- File: resources/views/admin/otp/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('OTP Requests Management') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Monitor and manage OTP access requests') }}</p>
            </div>
            @if($pendingCount > 0)
                <div class="flex items-center space-x-2">
                    <span class="bg-gradient-to-r from-red-600 to-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-lg animate-pulse">
                        {{ $pendingCount }} pending requests
                    </span>
                </div>
            @endif
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

        .tab-nav {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            border-radius: 12px;
            padding: 4px;
        }

        .tab-item {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
        }

        .tab-item:hover {
            color: #ffdd00;
            background: rgba(255, 221, 0, 0.1);
        }

        .tab-item.active {
            background: linear-gradient(135deg, #ffdd00, #f59e0b);
            color: #000;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 221, 0, 0.3);
        }

        .tab-badge {
            background: rgba(239, 68, 68, 0.9);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 8px;
        }

        .tab-item.active .tab-badge {
            background: rgba(0, 0, 0, 0.3);
            color: #000;
        }

        .bulk-actions-card {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.3);
            transition: all 0.3s ease;
        }

        .bulk-actions-card:hover {
            border-color: rgba(255, 221, 0, 0.5);
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

        .checkbox-custom {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffdd00;
        }

        .checkbox-custom:checked {
            background: #ffdd00;
            border-color: #ffdd00;
            color: #000;
        }

        .otp-request-card {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.2);
            transition: all 0.3s ease;
        }

        .otp-request-card:hover {
            border-color: rgba(255, 221, 0, 0.4);
            transform: translateY(-1px);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .status-approved {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-denied {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-expired {
            background: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }

        .status-used {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
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

        .btn-primary:disabled {
            background: rgba(75, 85, 99, 0.5);
            color: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
            transform: none;
        }

        .btn-approve {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
            transition: all 0.3s ease;
        }

        .btn-approve:hover {
            background: rgba(34, 197, 94, 0.3);
            border-color: #22c55e;
            transform: scale(1.05);
        }

        .btn-deny {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
        }

        .btn-deny:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: #ef4444;
            transform: scale(1.05);
        }

        .btn-view {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: rgba(59, 130, 246, 0.3);
            border-color: #3b82f6;
            transform: scale(1.05);
        }

        .expand-button {
            background: rgba(255, 221, 0, 0.1);
            color: #ffdd00;
            border: 1px solid rgba(255, 221, 0, 0.3);
            transition: all 0.3s ease;
        }

        .expand-button:hover {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
            transform: scale(1.1);
        }

        .otp-code {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            padding: 8px 12px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            color: #ffdd00;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .empty-state {
            background: rgba(255, 221, 0, 0.05);
            border: 2px dashed rgba(255, 221, 0, 0.3);
            border-radius: 16px;
            padding: 48px;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .detail-label {
            color: #ffdd00;
            font-weight: 600;
        }

        .detail-value {
            color: rgba(255, 255, 255, 0.9);
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Filter Tabs -->
            <div class="mb-8 animate-fade-in">
                <nav class="tab-nav">
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.otp.index', ['status' => 'pending']) }}" 
                           class="tab-item {{ $status === 'pending' ? 'active' : '' }}">
                            Pending
                            @if($pendingCount > 0 && $status === 'pending')
                                <span class="tab-badge">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.otp.index', ['status' => 'approved']) }}" 
                           class="tab-item {{ $status === 'approved' ? 'active' : '' }}">
                            Approved
                        </a>
                        <a href="{{ route('admin.otp.index', ['status' => 'denied']) }}" 
                           class="tab-item {{ $status === 'denied' ? 'active' : '' }}">
                            Denied
                        </a>
                        <a href="{{ route('admin.otp.index', ['status' => 'expired']) }}" 
                           class="tab-item {{ $status === 'expired' ? 'active' : '' }}">
                            Expired
                        </a>
                        <a href="{{ route('admin.otp.index', ['status' => 'used']) }}" 
                           class="tab-item {{ $status === 'used' ? 'active' : '' }}">
                            Used
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Bulk Actions (for pending requests) -->
            @if($status === 'pending' && $otpRequests->count() > 0)
                <div class="bulk-actions-card rounded-2xl p-6 mb-8 animate-fade-in" x-data="bulkActions()">
                    <form @submit.prevent="submitBulkApproval" class="flex flex-col md:flex-row md:items-center gap-4">
                        @csrf
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" 
                                   x-model="selectAll" 
                                   @change="toggleAll"
                                   class="rounded border-gray-600 text-yellow-400 bg-transparent focus:ring-yellow-400 checkbox-custom">
                            <span class="text-sm text-gray-300 font-medium">Select All</span>
                        </div>
                        
                        <div class="flex-1">
                            <input type="text" 
                                   x-model="bulkNotes"
                                   placeholder="Optional notes for bulk approval..."
                                   class="w-full rounded-xl px-4 py-3 form-input">
                        </div>
                        
                        <button type="submit" 
                                :disabled="selectedRequests.length === 0"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 btn-primary flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Approve Selected (<span x-text="selectedRequests.length"></span>)</span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- OTP Requests List -->
            <div class="glass-card rounded-2xl overflow-hidden animate-fade-in">
                @if($otpRequests->count() > 0)
                    <div class="divide-y divide-yellow-400/20">
                        @foreach($otpRequests as $request)
                            <div class="otp-request-card p-6" x-data="{ expanded: false }">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        @if($status === 'pending')
                                            <div class="flex items-center pt-1">
                                                <input type="checkbox" 
                                                       value="{{ $request->id }}"
                                                       x-model="selectedRequests"
                                                       class="rounded border-gray-600 text-yellow-400 bg-transparent focus:ring-yellow-400 checkbox-custom">
                                            </div>
                                        @endif
                                        
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg font-semibold text-white">{{ $request->user->name }}</h3>
                                                <span class="status-badge status-{{ $request->status }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center space-x-4">
                                                    <div>
                                                        <span class="detail-label">Purpose:</span>
                                                        <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $request->purpose)) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-4">
                                                    <div>
                                                        <span class="detail-label">Requested:</span>
                                                        <span class="detail-value">{{ $request->created_at->format('M d, Y H:i:s') }}</span>
                                                        <span class="text-gray-400">({{ $request->created_at->diffForHumans() }})</span>
                                                    </div>
                                                </div>
                                                @if($request->status === 'pending')
                                                    <div class="flex items-center space-x-4">
                                                        <div>
                                                            <span class="detail-label">Expires:</span>
                                                            <span class="detail-value">{{ $request->time_remaining }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3 ml-4">
                                        <button @click="expanded = !expanded" 
                                                class="p-2 rounded-lg expand-button">
                                            <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <a href="{{ route('admin.otp.show', $request) }}" 
                                           class="px-4 py-2 rounded-lg text-sm font-medium btn-view flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>View Details</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Expanded Content -->
                                <div x-show="expanded" x-collapse class="mt-6 pt-6 border-t border-yellow-400/20">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm mb-6">
                                        <div class="space-y-2">
                                            <div class="detail-label">OTP Code:</div>
                                            <div class="otp-code">{{ $request->otp_code }}</div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="detail-label">IP Address:</div>
                                            <div class="detail-value font-mono">{{ $request->ip_address }}</div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="detail-label">User Agent:</div>
                                            <div class="detail-value text-xs">{{ Str::limit($request->user_agent, 50) }}</div>
                                        </div>
                                    </div>

                                    @if($request->admin_notes)
                                        <div class="mb-6 p-4 bg-black/40 rounded-xl border border-yellow-400/20">
                                            <div class="detail-label mb-2">Admin Notes:</div>
                                            <p class="detail-value">{{ $request->admin_notes }}</p>
                                        </div>
                                    @endif

                                    @if($request->status === 'pending')
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <form method="POST" action="{{ route('admin.otp.approve', $request) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full sm:w-auto px-6 py-3 rounded-xl font-semibold transition-all duration-300 btn-approve flex items-center justify-center space-x-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Approve Request</span>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.otp.deny', $request) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="admin_notes" value="Denied without specific reason">
                                                <button type="submit" 
                                                        class="w-full sm:w-auto px-6 py-3 rounded-xl font-semibold transition-all duration-300 btn-deny flex items-center justify-center space-x-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Deny Request</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-6 border-t border-yellow-400/20">
                        {{ $otpRequests->appends(['status' => $status])->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="empty-state mx-auto max-w-md">
                            <svg class="mx-auto h-16 w-16 text-yellow-400/50 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-white mb-4">No {{ $status }} OTP requests</h3>
                            <p class="text-gray-400">
                                @if($status === 'pending')
                                    No pending OTP requests at this time. All clear!
                                @else
                                    No {{ $status }} OTP requests found.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function bulkActions() {
            return {
                selectAll: false,
                selectedRequests: [],
                bulkNotes: '',
                
                toggleAll() {
                    if (this.selectAll) {
                        this.selectedRequests = Array.from(document.querySelectorAll('input[type="checkbox"][value]')).map(cb => cb.value);
                    } else {
                        this.selectedRequests = [];
                    }
                },
                
                async submitBulkApproval() {
                    if (this.selectedRequests.length === 0) return;
                    
                    if (!confirm(`Are you sure you want to approve ${this.selectedRequests.length} OTP requests?`)) {
                        return;
                    }
                    
                    try {
                        const response = await fetch('{{ route("admin.otp.bulk-approve") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                otp_request_ids: this.selectedRequests,
                                admin_notes: this.bulkNotes
                            })
                        });
                        
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Failed to approve OTP requests. Please try again.');
                        }
                    } catch (error) {
                        alert('An error occurred. Please try again.');
                    }
                }
            }
        }
    </script>
</x-app-layout>