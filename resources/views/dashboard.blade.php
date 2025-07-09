<x-app-layout>
    <x-slot name="header">
        <div class="animate-slide-up">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold gradient-text">
                        {{ __('Dashboard') }}
                    </h2>
                    <p class="text-gray-300 mt-1">{{ __('Welcome back') }}, {{ auth()->user()->name }}!</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-emerald-400">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-sm">{{ __('System Operational') }}</span>
                    </div>
                    <div class="text-gray-300 text-sm">
                        {{ now()->format('M d, Y - H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Simplified Custom Styles -->
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(251, 191, 36, 0.3);
            border-color: rgba(251, 191, 36, 0.4);
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.5s ease-out;
        }

        .animate-scale-in {
            animation: scaleIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes slideUp {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        @keyframes scaleIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .progress-bar {
            background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.4), transparent);
            animation: progress-shimmer 2s infinite;
        }

        @keyframes progress-shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .dashboard-container {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 30%, #2a2418 50%, #1a1a1a 70%, #000000 100%);
            min-height: 100vh;
        }

        .stat-card {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .content-card {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(255, 221, 0, 0.25);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
        }

        .profile-card {
            background: rgba(42, 36, 24, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 221, 0, 0.1);
        }

        .activity-item {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 221, 0, 0.1);
            transition: all 0.2s ease;
        }

        .activity-item:hover {
            background: rgba(255, 221, 0, 0.05);
            border-color: rgba(255, 221, 0, 0.2);
            transform: translateX(2px);
        }

        .simple-button {
            transition: all 0.2s ease;
        }

        .simple-button:hover {
            transform: translateY(-1px);
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Overview Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Credentials -->
                <div class="card-hover stat-card rounded-xl p-6 animate-scale-in relative overflow-hidden h-40" style="animation-delay: 0.1s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div class="p-3 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-600">
                                <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414A6 6 0 0121 9z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-yellow-400 counter">{{ $totalCredentials ?? 0 }}</p>
                                <p class="text-xs text-gray-300">
                                    @if(($totalCredentials ?? 0) > 0)
                                        credentials available
                                    @else
                                        No credentials yet
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-300 mb-2">Total Credentials</p>
                            <div class="w-full bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full relative" style="width: {{ ($totalCredentials ?? 0) > 0 ? min((($totalCredentials ?? 0) / 20) * 100, 100) : 0 }}%">
                                    <div class="absolute inset-0 progress-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Categories -->
                <div class="card-hover stat-card rounded-xl p-6 animate-scale-in relative overflow-hidden h-40" style="animation-delay: 0.2s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div class="p-3 rounded-full bg-gradient-to-br from-amber-500 to-amber-600">
                                <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-yellow-400 counter">{{ $totalCategories ?? 8 }}</p>
                                <p class="text-xs text-gray-300">Ready to use</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-300 mb-2">Categories</p>
                            <div class="w-full bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-600 rounded-full relative" style="width: 100%">
                                    <div class="absolute inset-0 progress-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expiring Credentials -->
                <div class="card-hover stat-card rounded-xl p-6 animate-scale-in relative overflow-hidden h-40" style="animation-delay: 0.3s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div class="p-3 rounded-full bg-gradient-to-br from-orange-500 to-orange-600">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if(($expiringCredentials ?? 0) > 0)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-yellow-400 counter">{{ $expiringCredentials ?? 0 }}</p>
                                <p class="text-xs text-gray-300">
                                    {{ ($expiringCredentials ?? 0) > 0 ? 'Need attention' : 'All good' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-300 mb-2">Expiring Soon</p>
                            <div class="w-full bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-orange-500 to-orange-600 rounded-full relative" style="width: {{ ($expiringCredentials ?? 0) > 0 ? min((($expiringCredentials ?? 0) / 10) * 100, 100) : 0 }}%">
                                    <div class="absolute inset-0 progress-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expired Credentials -->
                <div class="card-hover stat-card rounded-xl p-6 animate-scale-in relative overflow-hidden h-40" style="animation-delay: 0.4s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div class="p-3 rounded-full bg-gradient-to-br from-red-500 to-red-600">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if(($expiredCredentials ?? 0) > 0)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-yellow-400 counter">{{ $expiredCredentials ?? 0 }}</p>
                                <p class="text-xs text-gray-300">
                                    {{ ($expiredCredentials ?? 0) > 0 ? 'Expired' : 'Clean slate' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-300 mb-2">Expired</p>
                            <div class="w-full bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-red-500 to-red-600 rounded-full relative" style="width: {{ ($expiredCredentials ?? 0) > 0 ? min((($expiredCredentials ?? 0) / 10) * 100, 100) : 0 }}%">
                                    <div class="absolute inset-0 progress-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
                
                <!-- Left Column - Quick Actions & Recent Activity -->
                <div class="lg:col-span-3 space-y-6">
                    
                    <!-- Quick Actions -->
                    <div class="content-card rounded-xl p-6 animate-slide-up" style="animation-delay: 0.5s">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-yellow-400 flex items-center">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-3 animate-pulse"></span>
                                {{ __('Quick Actions') }}
                            </h3>
                            <div class="text-xs text-gray-300">{{ __('Choose an action') }}</div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if(auth()->user()->role !== 'admin' || auth()->user()->hasValidOtp())
                                <x-primary-button 
                                    type="button" 
                                    onclick="window.location='{{ route('credentials.create') }}'"
                                    class="w-full px-6 py-4 bg-gradient-to-r from-yellow-600 to-yellow-500 text-black font-semibold rounded-lg hover:from-yellow-500 hover:to-yellow-400 simple-button"
                                >
                                    <div class="flex flex-col items-center space-y-2">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span class="text-sm">{{ __('Add Credential') }}</span>
                                    </div>
                                </x-primary-button>
                            @endif
                            
                            <x-secondary-button 
                                type="button" 
                                onclick="window.location='{{ route('credentials.index') }}'"
                                class="w-full px-6 py-4 bg-transparent border-2 border-yellow-400 text-yellow-400 font-semibold rounded-lg hover:bg-yellow-400 hover:text-black simple-button"
                            >
                                <div class="flex flex-col items-center space-y-2">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span class="text-sm">{{ __('View All') }}</span>
                                </div>
                            </x-secondary-button>
                            
                            @if(auth()->user()->isAdmin())
                                <x-secondary-button 
                                    type="button" 
                                    onclick="window.location='{{ route('admin.users.index') }}'"
                                    class="w-full px-6 py-4 bg-transparent border-2 border-amber-400 text-amber-400 font-semibold rounded-lg hover:bg-amber-400 hover:text-black simple-button"
                                >
                                    <div class="flex flex-col items-center space-y-2">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        <span class="text-sm">{{ __('Manage Users') }}</span>
                                    </div>
                                </x-secondary-button>
                            @elseif(!auth()->user()->hasValidOtp())
                                <x-secondary-button 
                                    type="button" 
                                    onclick="window.location='{{ route('otp.request') }}'"
                                    class="w-full px-6 py-4 bg-transparent border-2 border-green-400 text-green-400 font-semibold rounded-lg hover:bg-green-400 hover:text-black simple-button"
                                >
                                    <div class="flex flex-col items-center space-y-2">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414A6 6 0 0121 9z"/>
                                        </svg>
                                        <span class="text-sm">{{ __('Request OTP') }}</span>
                                    </div>
                                </x-secondary-button>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="content-card rounded-xl p-6 animate-slide-up" style="animation-delay: 0.6s">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-yellow-400 flex items-center">
                                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-3 animate-pulse"></span>
                                {{ __('Recent Activity') }}
                            </h3>
                            <a href="#" onclick="openActivityModal()" 
                               class="text-xs text-gray-300 hover:text-yellow-400 transition-colors">{{ __('View all') }}</a>
                        </div>
                        
                        <div class="space-y-4">
                            {{-- Dynamic Activity for Admin Users --}}
                            @if(auth()->user()->isAdmin() && isset($recentActivity) && $recentActivity->count() > 0)
                                @foreach($recentActivity->take(3) as $activity)
                                    <div class="activity-item flex items-center space-x-4 p-3 rounded-lg">
                                        <div class="w-10 h-10 bg-gradient-to-br from-{{ $activity['color'] ?? 'blue' }}-500 to-{{ $activity['color'] ?? 'blue' }}-600 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-white">{{ $activity['title'] ?? 'Unknown Activity' }}</p>
                                            <p class="text-xs text-gray-300">{{ $activity['subtitle'] ?? 'No details available' }}</p>
                                            <p class="text-xs text-gray-400">
                                                @if(isset($activity['time']) && $activity['time'] && method_exists($activity['time'], 'diffForHumans'))
                                                    {{ $activity['time']->diffForHumans() }}
                                                @else
                                                    {{ __('Recent') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- Dynamic Activity for IT Staff --}}
                            @elseif(!auth()->user()->isAdmin() && isset($myRecentAccess) && $myRecentAccess->count() > 0)
                                @foreach($myRecentAccess->take(3) as $access)
                                    <div class="activity-item flex items-center space-x-4 p-3 rounded-lg">
                                        <div class="w-10 h-10 bg-gradient-to-br from-{{ $access['color'] ?? 'green' }}-500 to-{{ $access['color'] ?? 'green' }}-600 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-white">{{ $access['title'] ?? 'Credential Access' }}</p>
                                            <p class="text-xs text-gray-300">{{ $access['subtitle'] ?? 'Details unavailable' }}</p>
                                            <p class="text-xs text-gray-400">
                                                @if(isset($access['time']) && $access['time'] && method_exists($access['time'], 'diffForHumans'))
                                                    {{ $access['time']->diffForHumans() }}
                                                @else
                                                    {{ __('Recent') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                            {{-- Fallback: Static Activity Items --}}
                            @else
                                <div class="activity-item flex items-center space-x-4 p-3 rounded-lg">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-white">{{ __('Welcome') }}, {{ auth()->user()->name }}!</p>
                                        <p class="text-xs text-gray-300">{{ __('Your account is ready') }}</p>
                                        <p class="text-xs text-gray-400">{{ now()->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <div class="activity-item flex items-center space-x-4 p-3 rounded-lg">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-white">{{ __('System operational') }}: All features ready</p>
                                        <p class="text-xs text-gray-300">{{ __('Security enabled and functional') }}</p>
                                        <p class="text-xs text-gray-400">{{ __('Active') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Stats & Status -->
                <div class="space-y-6">
                    
                    <!-- Activity Stats -->
                    <div class="content-card rounded-xl p-6 animate-slide-up" style="animation-delay: 0.7s">
                        <h3 class="text-lg font-bold text-yellow-400 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            {{ auth()->user()->isAdmin() ? __("Today's Statistics") : __('Your Activity') }}
                        </h3>
                        
                        <div class="space-y-4">
                            @if(auth()->user()->isAdmin() && isset($todayStats))
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('Credentials Accessed') }}</span>
                                    <span class="text-lg font-bold text-blue-400">{{ $todayStats['credentials_accessed_today'] ?? 0 }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('Active Users') }}</span>
                                    <span class="text-lg font-bold text-green-400">{{ $todayStats['unique_users_today'] ?? 0 }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('OTP Requests') }}</span>
                                    <span class="text-lg font-bold text-yellow-400">{{ $todayStats['otp_requests_today'] ?? 0 }}</span>
                                </div>
                            @elseif(!auth()->user()->isAdmin() && isset($userStats))
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('Total Accesses') }}</span>
                                    <span class="text-lg font-bold text-blue-400">{{ $userStats['total_access_count'] ?? 0 }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('This Week') }}</span>
                                    <span class="text-lg font-bold text-green-400">{{ $userStats['access_this_week'] ?? 0 }}</span>
                                </div>
                                
                                @if(isset($userStats['last_access']) && $userStats['last_access'] && method_exists($userStats['last_access'], 'diffForHumans'))
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-300">{{ __('Last Access') }}</span>
                                        <span class="text-xs text-gray-400">{{ $userStats['last_access']->diffForHumans() }}</span>
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('Account Status') }}</span>
                                    <span class="text-lg font-bold text-green-400">{{ __('Active') }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('Role') }}</span>
                                    <span class="text-sm text-yellow-400">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-300">{{ __('Member Since') }}</span>
                                    <span class="text-xs text-gray-400">{{ auth()->user()->created_at->format('M Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- OTP Status (Non-Admin Users) -->
                    @if(!auth()->user()->isAdmin())
                        <div class="content-card rounded-xl p-6 animate-slide-up" style="animation-delay: 0.8s">
                            <h3 class="text-lg font-bold text-yellow-400 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414A6 6 0 0121 9z"/>
                                </svg>
                                {{ __('OTP Status') }}
                            </h3>
                            
                            @if(auth()->user()->hasValidOtp())
                                <div class="p-3 bg-green-400/10 border border-green-400/20 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm text-green-400 font-medium">{{ __('OTP Active') }}</span>
                                    </div>
                                    <p class="text-xs text-green-300 mt-1">{{ __('You have access to credentials') }}</p>
                                    @if(auth()->user()->otp_session_expires_at && method_exists(auth()->user()->otp_session_expires_at, 'diffForHumans'))
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ __('Expires') }}: {{ auth()->user()->otp_session_expires_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <div class="p-3 bg-yellow-400/10 border border-yellow-400/20 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm text-yellow-400 font-medium">{{ __('OTP Required') }}</span>
                                    </div>
                                    <p class="text-xs text-yellow-300 mt-1">{{ __('Request OTP to access credentials') }}</p>
                                    <a href="{{ route('otp.request') }}" class="text-xs text-yellow-400 hover:text-yellow-300 underline mt-1 block">{{ __('Request Access') }}</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Modal -->
    <div id="activityModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75" onclick="closeActivityModal()"></div>

            <!-- Modal panel -->
            <div class="inline-block w-full max-w-4xl p-6 my-8 text-left align-middle transition-all transform bg-gray-900 shadow-xl rounded-2xl border border-yellow-400/30">
                <!-- Modal header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-yellow-400 flex items-center">
                        <span class="w-3 h-3 bg-emerald-400 rounded-full mr-3 animate-pulse"></span>
                        {{ __('All Recent Activity') }}
                    </h3>
                    <button onclick="closeActivityModal()" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="max-h-96 overflow-y-auto">
                    <div id="modalActivityContent" class="space-y-3">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="flex justify-end mt-6 pt-4 border-t border-gray-700">
                    <button onclick="closeActivityModal()" class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-yellow-500 text-black font-semibold rounded-lg hover:from-yellow-500 hover:to-yellow-400 transition-all duration-300">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample activity data for demonstration
        const allActivityData = [
            @if(auth()->user()->isAdmin() && isset($recentActivity))
                @foreach($recentActivity as $activity)
                {
                    icon: '{{ $activity['icon'] ?? 'shield' }}',
                    color: '{{ $activity['color'] ?? 'blue' }}',
                    title: '{{ $activity['title'] ?? 'Unknown Activity' }}',
                    subtitle: '{{ $activity['subtitle'] ?? 'No details available' }}',
                    time: '{{ isset($activity['time']) && $activity['time'] && method_exists($activity['time'], 'diffForHumans') ? $activity['time']->diffForHumans() : 'Recent' }}'
                },
                @endforeach
            @elseif(!auth()->user()->isAdmin() && isset($myRecentAccess))
                @foreach($myRecentAccess as $access)
                {
                    icon: '{{ $access['icon'] ?? 'key' }}',
                    color: '{{ $access['color'] ?? 'green' }}',
                    title: '{{ $access['title'] ?? 'Credential Access' }}',
                    subtitle: '{{ $access['subtitle'] ?? 'Details unavailable' }}',
                    time: '{{ isset($access['time']) && $access['time'] && method_exists($access['time'], 'diffForHumans') ? $access['time']->diffForHumans() : 'Recent' }}'
                },
                @endforeach
            @else
                {
                    icon: 'user',
                    color: 'blue',
                    title: '{{ __('Welcome') }}, {{ auth()->user()->name }}!',
                    subtitle: '{{ __('Your account is ready') }}',
                    time: '{{ now()->diffForHumans() }}'
                },
                {
                    icon: 'shield',
                    color: 'emerald',
                    title: '{{ __('System operational') }}: All features ready',
                    subtitle: '{{ __('Security enabled and functional') }}',
                    time: '{{ __('Active') }}'
                },
                {
                    icon: 'key',
                    color: 'yellow',
                    title: '{{ __('Credential Manager Active') }}',
                    subtitle: '{{ __('Ready to manage your credentials') }}',
                    time: '{{ __('Always available') }}'
                },
                {
                    icon: 'eye',
                    color: 'purple',
                    title: '{{ __('Security monitoring enabled') }}',
                    subtitle: '{{ __('All access attempts are logged') }}',
                    time: '{{ __('Continuous') }}'
                }
            @endif
        ];

        function getIconSvg(iconType) {
            const icons = {
                eye: '<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>',
                clipboard: '<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/><path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2V5h-2v6z"/></svg>',
                user: '<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>',
                shield: '<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                key: '<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/></svg>',
                default: '<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[iconType] || icons.default;
        }

        function openActivityModal() {
            const modal = document.getElementById('activityModal');
            const content = document.getElementById('modalActivityContent');
            
            // Populate modal with all activity data
            content.innerHTML = '';
            allActivityData.forEach(activity => {
                const activityHtml = `
                    <div class="activity-item flex items-center space-x-4 p-3 rounded-lg">
                        <div class="w-10 h-10 bg-gradient-to-br from-${activity.color}-500 to-${activity.color}-600 rounded-full flex items-center justify-center">
                            ${getIconSvg(activity.icon)}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white">${activity.title}</p>
                            <p class="text-xs text-gray-300">${activity.subtitle}</p>
                            <p class="text-xs text-gray-400">${activity.time}</p>
                        </div>
                    </div>
                `;
                content.innerHTML += activityHtml;
            });
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeActivityModal() {
            const modal = document.getElementById('activityModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeActivityModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Simple counter animation for stats
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                if (target > 0) {
                    const increment = target / 30;
                    let current = 0;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target;
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current);
                        }
                    }, 50);
                }
            });

            // Auto-hide alert messages
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.transition = 'all 0.3s ease-out';
                    alert.style.transform = 'translateY(-20px)';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        });
    </script>
</x-app-layout>