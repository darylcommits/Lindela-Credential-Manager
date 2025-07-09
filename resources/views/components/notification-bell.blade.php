{{-- resources/views/components/notification-bell.blade.php --}}
@props(['notifications' => [], 'unreadNotificationsCount' => 0])

<div class="relative" x-data="{ 
    notificationsOpen: false, 
    notifications: @js($notifications),
    unreadCount: {{ $unreadNotificationsCount }},
    loading: false,
    
    async fetchNotifications() {
        if (this.loading) return;
        this.loading = true;
        
        try {
            const response = await fetch('{{ route('dashboard.notifications') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            }
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
        } finally {
            this.loading = false;
        }
    },
    
    async markAsRead(notificationId) {
        try {
            await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                }
            });
            
            // Update local state
            this.notifications = this.notifications.map(n => 
                n.id === notificationId ? {...n, read: true} : n
            );
            this.unreadCount = Math.max(0, this.unreadCount - 1);
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    },
    
    formatTime(time) {
        const date = new Date(time);
        const now = new Date();
        const diffInHours = (now - date) / (1000 * 60 * 60);
        
        if (diffInHours < 1) {
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            return diffInMinutes <= 1 ? 'Just now' : `${diffInMinutes}m ago`;
        } else if (diffInHours < 24) {
            return `${Math.floor(diffInHours)}h ago`;
        } else {
            const diffInDays = Math.floor(diffInHours / 24);
            return `${diffInDays}d ago`;
        }
    },
    
    getNotificationIcon(notification) {
        const iconMap = {
            'key': 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z',
            'exclamation-triangle': 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
            'shield-exclamation': 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
            'check-circle': 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'x-circle': 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'clock': 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
            'user-plus': 'M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z',
            'bell': 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'
        };
        
        return iconMap[notification.icon] || iconMap['bell'];
    },
    
    getNotificationColor(notification) {
        const colorMap = {
            'yellow': 'text-yellow-500 bg-yellow-500/10',
            'red': 'text-red-500 bg-red-500/10',
            'green': 'text-green-500 bg-green-500/10',
            'blue': 'text-blue-500 bg-blue-500/10',
            'orange': 'text-orange-500 bg-orange-500/10',
            'purple': 'text-purple-500 bg-purple-500/10'
        };
        
        return colorMap[notification.color] || colorMap['blue'];
    }
}" 
@click.away="notificationsOpen = false"
x-init="
    // Fetch notifications every 30 seconds when tab is active
    let notificationInterval;
    const startPolling = () => {
        if (notificationInterval) clearInterval(notificationInterval);
        notificationInterval = setInterval(() => {
            if (!document.hidden) {
                fetchNotifications();
            }
        }, 30000);
    };
    
    // Start polling
    startPolling();
    
    // Pause/resume polling when tab visibility changes
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (notificationInterval) clearInterval(notificationInterval);
        } else {
            fetchNotifications();
            startPolling();
        }
    });
">
    <!-- Notification Bell Button -->
    <button 
        @click="notificationsOpen = !notificationsOpen; if(notificationsOpen && !loading) fetchNotifications()"
        class="relative p-2 text-gray-300 hover:text-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 rounded-lg transition-all duration-200 nav-button group"
        :class="{ 'text-yellow-400': notificationsOpen }"
        title="Notifications"
    >
        <!-- Bell Icon -->
        <svg class="w-6 h-6 transition-transform duration-200 group-hover:scale-110" 
             :class="{ 'animate-pulse': loading, 'animate-bounce': unreadCount > 0 && !notificationsOpen }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <!-- Notification Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 scale-50"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-50"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-black bg-yellow-400 rounded-full min-w-[18px] h-[18px] shadow-lg border-2 border-black">
        </span>
    </button>

    <!-- Notifications Dropdown -->
    <div x-show="notificationsOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute right-0 mt-2 w-96 nav-dropdown rounded-xl shadow-lg z-50 max-h-[32rem] overflow-hidden"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-yellow-400/20 bg-black/30">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-yellow-400 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    Notifications
                </h3>
                <div class="flex items-center space-x-2">
                    <span x-show="unreadCount > 0" 
                          class="px-2 py-1 text-xs font-medium text-black bg-yellow-400 rounded-full"
                          x-text="unreadCount + ' new'">
                    </span>
                    <button @click="fetchNotifications()" 
                            :disabled="loading"
                            class="p-1 text-gray-400 hover:text-yellow-400 transition-colors disabled:opacity-50"
                            title="Refresh notifications">
                        <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto">
            <!-- Loading State -->
            <div x-show="loading && notifications.length === 0" class="px-4 py-8 text-center">
                <div class="inline-flex items-center text-gray-400">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading notifications...
                </div>
            </div>

            <!-- No Notifications -->
            <div x-show="!loading && notifications.length === 0" class="px-4 py-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="text-gray-400 text-base font-medium">No notifications yet</p>
                <p class="text-gray-500 text-sm mt-2">We'll notify you when something important happens</p>
            </div>

            <!-- Notification Items -->
            <template x-for="notification in notifications" :key="notification.id">
                <div class="relative group">
                    <a :href="notification.url || '#'" 
                       @click="if (notification.url && !notification.read) markAsRead(notification.id)"
                       class="block px-4 py-4 hover:bg-yellow-400/5 transition-colors duration-200 border-b border-gray-700/30 last:border-b-0"
                       :class="{ 'bg-yellow-400/5': !notification.read }">
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                     :class="getNotificationColor(notification)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getNotificationIcon(notification)"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <p class="text-sm font-semibold text-white leading-5" 
                                       :class="{ 'text-yellow-400': !notification.read }"
                                       x-text="notification.title"></p>
                                    <span x-show="!notification.read" class="flex-shrink-0 ml-2">
                                        <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-300 mt-1 leading-5 line-clamp-2" x-text="notification.message"></p>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs text-gray-400" x-text="formatTime(notification.time)"></p>
                                    <span x-show="notification.type" 
                                          class="text-xs text-gray-500 capitalize px-2 py-1 bg-gray-700/50 rounded"
                                          x-text="notification.type.replace('_', ' ')"></span>
                                </div>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Mark as read button for unread notifications -->
                    <button x-show="!notification.read" 
                            @click.prevent.stop="markAsRead(notification.id)"
                            class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-yellow-400 transition-all duration-200"
                            title="Mark as read">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div x-show="notifications.length > 0" class="px-4 py-3 border-t border-yellow-400/20 bg-black/20">
            <div class="flex items-center justify-between">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.logs.system') }}" 
                       class="text-sm text-yellow-400 hover:text-yellow-300 transition-colors font-medium">
                        View All Activity
                    </a>
                @else
                    <a href="{{ route('otp.status') }}" 
                       class="text-sm text-yellow-400 hover:text-yellow-300 transition-colors font-medium">
                        View OTP Status
                    </a>
                @endif
                
                <span x-show="unreadCount > 0" class="text-xs text-gray-400">
                    <button @click="$refs.markAllRead?.click()" class="hover:text-yellow-400 transition-colors">
                        Mark all read
                    </button>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Custom notification styles -->
<style>
/* Enhanced notification dropdown */
.nav-dropdown {
    backdrop-filter: blur(15px);
    background: rgba(0, 0, 0, 0.9);
    border: 1px solid rgba(251, 191, 36, 0.3);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(251, 191, 36, 0.1);
}

/* Notification item hover effects */
.notification-item:hover {
    background: rgba(251, 191, 36, 0.05);
}

/* Custom scrollbar for notifications */
.max-h-80 {
    scrollbar-width: thin;
    scrollbar-color: rgba(251, 191, 36, 0.3) transparent;
}

.max-h-80::-webkit-scrollbar {
    width: 4px;
}

.max-h-80::-webkit-scrollbar-track {
    background: transparent;
}

.max-h-80::-webkit-scrollbar-thumb {
    background: rgba(251, 191, 36, 0.3);
    border-radius: 2px;
}

.max-h-80::-webkit-scrollbar-thumb:hover {
    background: rgba(251, 191, 36, 0.5);
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Pulse animation for unread count */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(251, 191, 36, 0);
    }
}

.animate-pulse-glow {
    animation: pulse-glow 2s infinite;
}
</style>