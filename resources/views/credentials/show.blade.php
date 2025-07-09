{{-- File: resources/views/credentials/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ $credential->name }}
                </h2>
                <p class="text-gray-300 mt-1">{{ $credential->category->name ?? 'Uncategorized' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('credentials.edit', $credential) }}" 
                   class="bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>{{ __('Edit') }}</span>
                </a>
                <a href="{{ route('credentials.index') }}" 
                   class="bg-gradient-to-r from-gray-600 to-gray-500 text-white hover:from-gray-500 hover:to-gray-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>{{ __('Back to List') }}</span>
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

        .credential-field {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .credential-field:focus {
            outline: none;
            border-color: #ffdd00;
            background: rgba(255, 221, 0, 0.1);
            box-shadow: 0 0 15px rgba(255, 221, 0, 0.2);
        }

        .copy-btn {
            background: rgba(255, 221, 0, 0.1);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffdd00;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
            transform: scale(1.05);
        }

        .action-btn {
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }

        .status-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .password-strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
        }

        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            0% { transform: scale(0.9) translateY(-10px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        .section-header {
            border-bottom: 2px solid rgba(255, 221, 0, 0.2);
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #ffdd00, #f59e0b);
        }

        .info-card {
            background: rgba(255, 221, 0, 0.05);
            border: 1px solid rgba(255, 221, 0, 0.2);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            background: rgba(255, 221, 0, 0.1);
            border-color: rgba(255, 221, 0, 0.4);
        }

        .access-log-item {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.1);
            transition: all 0.3s ease;
        }

        .access-log-item:hover {
            border-color: rgba(255, 221, 0, 0.3);
            transform: translateX(5px);
        }

        .backup-code-field {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 221, 0, 0.2);
            color: #ffffff;
            font-family: 'Courier New', monospace;
        }

        .website-link {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #60a5fa;
            transition: all 0.3s ease;
        }

        .website-link:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: #60a5fa;
            transform: scale(1.05);
        }

        .strength-indicator {
            background: rgba(255, 221, 0, 0.1);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .toast {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            0% { transform: translateX(100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Credential Details -->
                    <div class="glass-card rounded-2xl p-8 animate-fade-in">
                        <div class="flex items-start justify-between mb-8 pb-6 section-header">
                            <div class="flex items-center space-x-4">
                                <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, {{ $credential->category->color ?? '#ffdd00' }}, {{ $credential->category->color ?? '#f59e0b' }})"></div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">{{ $credential->name }}</h3>
                                    <p class="text-gray-400 mt-1">{{ $credential->category->name ?? 'Uncategorized' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end space-y-3">
                                @if($credential->isExpired())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500/20 text-red-400 status-badge">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Expired {{ $credential->expires_at->diffForHumans() }}
                                    </span>
                                @elseif($credential->isExpiringSoon())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-500/20 text-orange-400 status-badge">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        Expires {{ $credential->expires_at->diffForHumans() }}
                                    </span>
                                @elseif($credential->expires_at && !$credential->password_never_expires)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400 status-badge">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Expires {{ $credential->expires_at->diffForHumans() }}
                                    </span>
                                @elseif($credential->password_never_expires)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500/20 text-blue-400 status-badge">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Never Expires
                                    </span>
                                @endif
                                
                                @if($credential->password_strength)
                                    <div class="strength-indicator">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-sm font-medium {{ $credential->password_strength_color }} text-yellow-400">
                                                {{ $credential->password_strength_text ?? 'Good' }}
                                            </span>
                                        </div>
                                        <div class="flex space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <div class="password-strength-bar w-6 {{ $i <= ($credential->password_strength ?? 3) ? 'bg-yellow-400' : 'bg-gray-600' }}"></div>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @if($credential->username)
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-yellow-400 mb-2">Username</label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" value="{{ $credential->username }}" readonly 
                                           class="flex-1 rounded-xl px-4 py-3 credential-field">
                                    <button onclick="copyToClipboard('{{ $credential->username }}', 'username', {{ $credential->id }})" 
                                            class="copy-btn px-4 py-3 rounded-xl transition-all duration-300" title="Copy username">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endif

                            @if($credential->email)
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-yellow-400 mb-2">Email</label>
                                <div class="flex items-center space-x-3">
                                    <input type="email" value="{{ $credential->email }}" readonly 
                                           class="flex-1 rounded-xl px-4 py-3 credential-field">
                                    <button onclick="copyToClipboard('{{ $credential->email }}', 'email', {{ $credential->id }})" 
                                            class="copy-btn px-4 py-3 rounded-xl transition-all duration-300" title="Copy email">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endif

                            <div class="md:col-span-2 space-y-3">
                                <label class="block text-sm font-semibold text-yellow-400 mb-2">Password</label>
                                <div class="flex items-center space-x-3" x-data="{ showPassword: false }">
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           value="{{ $credential->password }}" 
                                           readonly 
                                           class="flex-1 rounded-xl px-4 py-3 credential-field font-mono">
                                    <button type="button" @click="showPassword = !showPassword" 
                                            class="copy-btn px-4 py-3 rounded-xl transition-all duration-300" title="Toggle password visibility">
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                        </svg>
                                    </button>
                                    <button onclick="copyToClipboard('{{ $credential->password }}', 'password', {{ $credential->id }})" 
                                            class="copy-btn px-4 py-3 rounded-xl transition-all duration-300" title="Copy password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @if($credential->website_url)
                            <div class="md:col-span-2 space-y-3">
                                <label class="block text-sm font-semibold text-yellow-400 mb-2">Website</label>
                                <div class="flex items-center space-x-3">
                                    <input type="url" value="{{ $credential->website_url }}" readonly 
                                           class="flex-1 rounded-xl px-4 py-3 credential-field">
                                    <a href="{{ $credential->website_url }}" target="_blank" 
                                       class="website-link px-4 py-3 rounded-xl transition-all duration-300 flex items-center" title="Open website">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($credential->backup_codes && count($credential->backup_codes) > 0)
                        <div class="mt-8 pt-6 border-t border-yellow-400/20">
                            <label class="block text-lg font-semibold text-yellow-400 mb-4">Backup Codes</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($credential->backup_codes as $index => $code)
                                    <div class="flex items-center space-x-3">
                                        <span class="text-xs text-gray-400 w-6">{{ $index + 1 }}.</span>
                                        <input type="text" value="{{ $code }}" readonly 
                                               class="flex-1 rounded-lg px-3 py-2 backup-code-field text-sm">
                                        <button onclick="copyToClipboard('{{ $code }}', 'backup_code')" 
                                                class="copy-btn px-3 py-2 rounded-lg transition-all duration-300" title="Copy backup code">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($credential->notes)
                        <div class="mt-8 pt-6 border-t border-yellow-400/20">
                            <label class="block text-lg font-semibold text-yellow-400 mb-4">Notes</label>
                            <div class="bg-black/40 rounded-xl p-4 text-gray-300 border border-yellow-400/20">
                                {!! nl2br(e($credential->notes)) !!}
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Access History -->
                    <div class="glass-card rounded-2xl animate-fade-in">
                        <div class="p-6 pb-4 section-header">
                            <h3 class="text-xl font-bold text-white">Recent Access History</h3>
                        </div>
                        <div class="space-y-3 p-6 pt-4">
                            @forelse($recentLogs ?? [] as $log)
                            <div class="access-log-item rounded-xl p-4">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $log->action_badge_color ?? 'bg-blue-500/20 text-blue-400' }}">
                                            {{ ucfirst($log->action ?? 'View') }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-white">{{ $log->user->name ?? 'Unknown User' }}</p>
                                        <div class="mt-1 flex items-center space-x-2 text-xs text-gray-400">
                                            <span>{{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : 'Unknown date' }}</span>
                                            <span>•</span>
                                            <span>{{ $log->ip_address ?? 'Unknown IP' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-gray-400">No access history available</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="glass-card rounded-2xl p-6 animate-fade-in">
                        <h3 class="text-xl font-bold text-white mb-6 pb-4 section-header">Quick Actions</h3>
                        <div class="space-y-4">
                            <a href="{{ route('credentials.edit', $credential) }}" 
                               class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2 action-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Edit Credential</span>
                            </a>
                            
                            <button onclick="showDeleteModal()" 
                                    class="w-full bg-gradient-to-r from-red-600 to-red-500 text-white hover:from-red-500 hover:to-red-400 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2 action-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span>Delete Credential</span>
                            </button>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="glass-card rounded-2xl p-6 animate-fade-in">
                        <h3 class="text-xl font-bold text-white mb-6 pb-4 section-header">Information</h3>
                        <div class="space-y-4">
                            <div class="info-card rounded-lg p-3">
                                <dt class="text-sm font-medium text-gray-400 mb-1">Created by</dt>
                                <dd class="text-sm text-white font-semibold">{{ $credential->creator->name ?? 'Unknown' }}</dd>
                            </div>
                            <div class="info-card rounded-lg p-3">
                                <dt class="text-sm font-medium text-gray-400 mb-1">Created on</dt>
                                <dd class="text-sm text-white font-semibold">{{ $credential->created_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                            <div class="info-card rounded-lg p-3">
                                <dt class="text-sm font-medium text-gray-400 mb-1">Last updated</dt>
                                <dd class="text-sm text-white font-semibold">{{ $credential->updated_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                            @if($credential->last_accessed_at ?? false)
                            <div class="info-card rounded-lg p-3">
                                <dt class="text-sm font-medium text-gray-400 mb-1">Last accessed</dt>
                                <dd class="text-sm text-white font-semibold">{{ $credential->last_accessed_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 modal-overlay hidden items-center justify-center z-50">
        <div class="glass-card rounded-xl p-6 max-w-md w-full mx-4 modal-content">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-500/20 mb-4">
                    <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">{{ __('Delete Credential') }}</h3>
                <p class="text-gray-400 mb-2">{{ __('Are you sure you want to delete') }}</p>
                <p class="text-yellow-400 font-semibold mb-4">"{{ $credential->name }}"</p>
                <p class="text-gray-400 text-sm mb-6">{{ __('This action cannot be undone and all associated data will be permanently removed.') }}</p>
                
                <div class="flex justify-center space-x-4">
                    <button onclick="closeDeleteModal()" 
                            class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105">
                        {{ __('Cancel') }}
                    </button>
                    <form method="POST" action="{{ route('credentials.destroy', $credential) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>{{ __('Delete Permanently') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Copy to clipboard functionality
        async function copyToClipboard(text, field, credentialId = null) {
            try {
                await navigator.clipboard.writeText(text);
                
                // Show success toast
                showToast(`${field.charAt(0).toUpperCase() + field.slice(1)} copied to clipboard!`, 'success');
                
                // Log the copy action if credential ID is provided
                if (credentialId) {
                    fetch(`/credentials/${credentialId}/copy`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ field: field })
                    }).catch(err => {
                        console.log('Copy logging failed:', err);
                    });
                }
            } catch (err) {
                showToast('Failed to copy to clipboard', 'error');
                console.error('Copy failed:', err);
            }
        }

        // Delete modal functions
        function showDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Focus on cancel button for accessibility
                setTimeout(() => {
                    const cancelBtn = modal.querySelector('button[onclick="closeDeleteModal()"]');
                    if (cancelBtn) cancelBtn.focus();
                }, 100);
            }
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Toast notification system
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transition-all duration-300 transform translate-x-full toast ${
                type === 'success' ? 'bg-green-500/90 text-white border border-green-400' : 
                type === 'error' ? 'bg-red-500/90 text-white border border-red-400' : 
                'bg-blue-500/90 text-white border border-blue-400'
            }`;
            
            const icon = type === 'success' ? 
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' :
                type === 'error' ?
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' :
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            
            toast.innerHTML = `<div class="flex items-center">${icon}<span class="font-semibold">${message}</span></div>`;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Modal event listeners
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });
            }

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });
        });
    </script>
</x-app-layout>