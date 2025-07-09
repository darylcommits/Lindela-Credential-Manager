<nav x-data="{ open: false }" class="glass-effect border-b border-yellow-400/20 sticky top-0 z-50 animate-fade-in">
    <!-- Navigation Styles -->
    <style>
        .nav-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #fbbf24, #f59e0b);
            transition: left 0.3s ease;
        }
        
        .nav-link:hover::before,
        .nav-link.active::before {
            left: 0;
        }

        .nav-link:hover {
            color: #fbbf24;
            transform: translateY(-2px);
        }

        .hamburger-line {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hamburger-active .line-1 {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .hamburger-active .line-2 {
            opacity: 0;
            transform: translateX(20px);
        }

        .hamburger-active .line-3 {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        .user-avatar {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 10px rgba(251, 191, 36, 0.3); }
            to { box-shadow: 0 0 20px rgba(251, 191, 36, 0.6); }
        }

        .nav-dropdown {
            backdrop-filter: blur(15px);
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(251, 191, 36, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(251, 191, 36, 0.1);
        }

        .mobile-nav {
            backdrop-filter: blur(15px);
            background: rgba(0, 0, 0, 0.95);
            border-bottom: 1px solid rgba(251, 191, 36, 0.2);
        }

        /* Enhanced button hover effects */
        .nav-button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
        }

        /* Improved dropdown animations */
        .dropdown-menu {
            transform-origin: top;
        }

        /* Logo enhancement */
        .logo-container:hover .logo-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .logo-icon {
            transition: transform 0.3s ease;
        }
    </style>

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group logo-container">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center shadow-lg user-avatar logo-icon">
                            <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                            </svg>
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-xl font-bold gradient-text">Lindela Travel & Tours</div>
                            <div class="text-xs text-gray-400">Credential Management</div>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-yellow-400 active' : 'text-gray-300 hover:text-yellow-400' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"></path>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('credentials.index')" :active="request()->routeIs('credentials.*')"
                        class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('credentials.*') ? 'text-yellow-400 active' : 'text-gray-300 hover:text-yellow-400' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414A6 6 0 0121 9z"></path>
                        </svg>
                        {{ __('Credentials') }}
                    </x-nav-link>
                    
                    @if(!auth()->user()->isAdmin())
                        <x-nav-link :href="route('otp.request')" :active="request()->routeIs('otp.*')"
                            class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('otp.*') ? 'text-yellow-400 active' : 'text-gray-300 hover:text-yellow-400' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0h-2m-1-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            {{ __('OTP Request') }}
                        </x-nav-link>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                        <!-- Admin Menu - FIXED DROPDOWN -->
                        <div class="relative" x-data="{ adminOpen: false }">
                            <button 
                                @click="adminOpen = !adminOpen" 
                                @click.away="adminOpen = false"
                                class="nav-link nav-button inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 focus:outline-none transition-all duration-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>Admin</div>
                                <svg class="ml-1 h-4 w-4 transform transition-transform duration-200" 
                                     :class="{'rotate-180': adminOpen}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="adminOpen" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute left-0 mt-2 w-56 nav-dropdown rounded-xl shadow-lg z-50 dropdown-menu"
                                 style="display: none;">
                                <div class="py-2">
                                    <a href="{{ route('admin.users.index') }}" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 transition-all duration-200 group">
                                        <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        {{ __('Manage Users') }}
                                    </a>
                                    <a href="{{ route('admin.otp.index') }}" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 transition-all duration-200 group">
                                        <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ __('OTP Requests') }}
                                    </a>
                                    <a href="{{ route('admin.logs.system') }}" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 transition-all duration-200 group">
                                        <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        {{ __('System Logs') }}
                                    </a>
                                    <a href="{{ route('admin.reports.index') }}" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 transition-all duration-200 group">
                                        <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ __('Reports') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Side - Notifications and User Menu -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 sm:space-x-4">
                
                <!-- Notification Bell -->
                @include('components.notification-bell', [
                    'notifications' => $notifications ?? [],
                    'unreadNotificationsCount' => $unreadNotificationsCount ?? 0
                ])

                <!-- Settings Dropdown - FIXED -->
                <div class="relative" x-data="{ userOpen: false }">
                    <button 
                        @click="userOpen = !userOpen"
                        @click.away="userOpen = false" 
                        class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-gradient-to-r from-yellow-600/20 to-yellow-400/20 border border-yellow-400/30 text-yellow-400 hover:from-yellow-600/30 hover:to-yellow-400/30 transition-all duration-300 nav-button">
                        <div class="w-8 h-8 user-avatar rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-black">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                            </span>
                        </div>
                        <div class="text-left hidden md:block">
                            <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-400">{{ ucfirst(auth()->user()->role ?? 'User') }}</div>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform duration-200" 
                             :class="{'rotate-180': userOpen}" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="userOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-48 nav-dropdown rounded-xl shadow-lg z-50 dropdown-menu"
                         style="display: none;">
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center px-4 py-3 text-sm text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/10 transition-all duration-200 group">
                                <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('Profile') }}
                            </a>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                    class="flex items-center w-full px-4 py-3 text-sm text-gray-300 hover:text-red-400 hover:bg-red-400/10 transition-all duration-200 group">
                                    <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" 
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-yellow-400 hover:bg-yellow-400/10 focus:outline-none focus:bg-yellow-400/10 focus:text-yellow-400 transition-all duration-200 nav-button"
                    :class="{'hamburger-active': open}">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <g class="hamburger-line">
                            <path class="line-1" :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16"/>
                            <path class="line-2" :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16"/>
                            <path class="line-3" :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18h16"/>
                            <path :class="{'hidden': !open, 'inline-flex': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </g>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu - FIXED -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform -translate-x-full"
         @click.away="open = false"
         class="sm:hidden mobile-nav border-t border-yellow-400/20"
         style="display: none;">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="flex items-center pl-3 pr-4 py-3 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-yellow-400 bg-yellow-400/10 border-r-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5' }} transition-all duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"></path>
                </svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('credentials.index')" :active="request()->routeIs('credentials.*')"
                class="flex items-center pl-3 pr-4 py-3 text-base font-medium {{ request()->routeIs('credentials.*') ? 'text-yellow-400 bg-yellow-400/10 border-r-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5' }} transition-all duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414A6 6 0 0121 9z"></path>
                </svg>
                {{ __('Credentials') }}
            </x-responsive-nav-link>
            
            @if(!auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('otp.request')" :active="request()->routeIs('otp.*')"
                    class="flex items-center pl-3 pr-4 py-3 text-base font-medium {{ request()->routeIs('otp.*') ? 'text-yellow-400 bg-yellow-400/10 border-r-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5' }} transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0h-2m-1-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    {{ __('OTP Request') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->isAdmin())
                <!-- Mobile Admin Links -->
                <div class="pt-2 border-t border-yellow-400/20">
                    <div class="px-3 py-2">
                        <div class="text-xs font-semibold text-yellow-400 uppercase tracking-wide">Admin</div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center pl-6 pr-4 py-3 text-base font-medium text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        {{ __('Manage Users') }}
                    </a>
                    <a href="{{ route('admin.otp.index') }}" class="flex items-center pl-6 pr-4 py-3 text-base font-medium text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('OTP Requests') }}
                    </a>
                    <a href="{{ route('admin.logs.system') }}" class="flex items-center pl-6 pr-4 py-3 text-base font-medium text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        {{ __('System Logs') }}
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center pl-6 pr-4 py-3 text-base font-medium text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('Reports') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-yellow-400/20">
            <div class="px-4 py-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 user-avatar rounded-full flex items-center justify-center">
                        <span class="text-sm font-bold text-black">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                        <div class="text-xs text-yellow-400">{{ ucfirst(auth()->user()->role ?? 'User') }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')"
                    class="flex items-center pl-3 pr-4 py-3 text-base font-medium text-gray-300 hover:text-yellow-400 hover:bg-yellow-400/5 transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                        class="flex items-center w-full pl-3 pr-4 py-3 text-base font-medium text-gray-300 hover:text-red-400 hover:bg-red-400/5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>