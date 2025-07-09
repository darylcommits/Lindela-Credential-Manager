<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SecureVault') }} - Premium Credential Management</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23fbbf24'><path d='M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z'/></svg>">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Global Theme Styles -->
        <style>
            :root {
                --gold-50: #fefce8;
                --gold-100: #fef3c7;
                --gold-200: #fde68a;
                --gold-300: #fcd34d;
                --gold-400: #fbbf24;
                --gold-500: #f59e0b;
                --gold-600: #d97706;
                --gold-700: #b45309;
                --gold-800: #92400e;
                --gold-900: #78350f;
            }

            * {
                scroll-behavior: smooth;
            }

            body {
                font-family: 'Inter', 'Figtree', system-ui, -apple-system, sans-serif;
                background: linear-gradient(135deg, #000000 0%, #111111 50%, #000000 100%);
                background-attachment: fixed;
                color: #ffffff;
                overflow-x: hidden;
            }

            .gradient-text {
                background: linear-gradient(135deg, var(--gold-400), var(--gold-500), var(--gold-600));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                font-weight: 700;
            }
            
            .glass-effect {
                backdrop-filter: blur(10px);
                background: rgba(0, 0, 0, 0.3);
                border: 1px solid rgba(251, 191, 36, 0.2);
            }

            .glass-header {
                backdrop-filter: blur(15px);
                background: rgba(0, 0, 0, 0.4);
                border-bottom: 1px solid rgba(251, 191, 36, 0.3);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }
            
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
                background: linear-gradient(90deg, var(--gold-400), var(--gold-500));
                transition: left 0.3s ease;
            }
            
            .nav-link:hover::before,
            .nav-link.active::before {
                left: 0;
            }

            .nav-link:hover {
                color: var(--gold-400);
                transform: translateY(-2px);
            }

            .animate-fade-in {
                animation: fadeIn 0.8s ease-out;
            }

            .animate-slide-down {
                animation: slideDown 0.6s ease-out;
            }

            .animate-glow {
                animation: glow 2s ease-in-out infinite alternate;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes slideDown {
                from { 
                    transform: translateY(-20px); 
                    opacity: 0; 
                }
                to { 
                    transform: translateY(0); 
                    opacity: 1; 
                }
            }

            @keyframes glow {
                from { 
                    box-shadow: 0 0 20px rgba(251, 191, 36, 0.3), 
                               0 0 40px rgba(251, 191, 36, 0.1); 
                }
                to { 
                    box-shadow: 0 0 30px rgba(251, 191, 36, 0.6), 
                               0 0 60px rgba(251, 191, 36, 0.2); 
                }
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }

            ::-webkit-scrollbar-track {
                background: #111111;
            }

            ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, var(--gold-600), var(--gold-400));
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, var(--gold-500), var(--gold-300));
            }

            /* Loading Animation */
            .page-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: #000000;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                transition: opacity 0.5s ease-out;
            }

            .loader-hidden {
                opacity: 0;
                pointer-events: none;
            }

            .loading-spinner {
                width: 60px;
                height: 60px;
                border: 3px solid rgba(251, 191, 36, 0.2);
                border-top: 3px solid var(--gold-400);
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Alert Styles */
            .alert {
                backdrop-filter: blur(10px);
                border: 1px solid;
                border-radius: 0.75rem;
                padding: 1rem;
                margin: 1rem 0;
                animation: slideDown 0.5s ease-out;
            }

            .alert-success {
                background: rgba(34, 197, 94, 0.1);
                border-color: rgba(34, 197, 94, 0.3);
                color: #4ade80;
            }

            .alert-error {
                background: rgba(239, 68, 68, 0.1);
                border-color: rgba(239, 68, 68, 0.3);
                color: #f87171;
            }

            .alert-warning {
                background: rgba(251, 191, 36, 0.1);
                border-color: rgba(251, 191, 36, 0.3);
                color: var(--gold-400);
            }

            /* Form Styles */
            input, select, textarea {
                background: rgba(0, 0, 0, 0.3) !important;
                border: 1px solid rgba(251, 191, 36, 0.2) !important;
                color: #ffffff !important;
                transition: all 0.3s ease !important;
            }

            input:focus, select:focus, textarea:focus {
                border-color: var(--gold-400) !important;
                box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1) !important;
                outline: none !important;
            }

            input::placeholder, textarea::placeholder {
                color: #9ca3af !important;
            }

            /* Button Base Styles */
            .btn-primary {
                background: linear-gradient(135deg, var(--gold-600), var(--gold-500)) !important;
                color: #000000 !important;
                border: none !important;
                font-weight: 600 !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, var(--gold-500), var(--gold-400)) !important;
                transform: translateY(-2px) scale(1.05) !important;
                box-shadow: 0 10px 25px rgba(251, 191, 36, 0.3) !important;
            }

            .btn-secondary {
                background: transparent !important;
                border: 2px solid var(--gold-400) !important;
                color: var(--gold-400) !important;
                font-weight: 600 !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .btn-secondary:hover {
                background: var(--gold-400) !important;
                color: #000000 !important;
                transform: translateY(-2px) scale(1.05) !important;
                box-shadow: 0 10px 25px rgba(251, 191, 36, 0.3) !important;
            }

            /* FIX: Enhanced status badge styles for user management */
            .status-badge {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .status-badge:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            }

            .status-badge.loading {
                opacity: 0.7;
                cursor: not-allowed;
                animation: pulse 1.5s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 0.7; }
                50% { opacity: 1; }
            }

            /* Enhanced checkbox styles */
            .checkbox-container input[type="checkbox"] {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                width: 24px;
                height: 24px;
                border: 2px solid var(--gold-400);
                border-radius: 6px;
                background: rgba(0, 0, 0, 0.6);
                position: relative;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .checkbox-container input[type="checkbox"]:checked {
                background: linear-gradient(135deg, var(--gold-600), var(--gold-400));
                border-color: var(--gold-400);
            }

            .checkbox-container input[type="checkbox"]:checked::after {
                content: '✓';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: #000;
                font-size: 14px;
                font-weight: bold;
            }
        </style>
    </head>
    <body class="font-sans antialiased animate-fade-in">
        <!-- Page Loader -->
        <div id="pageLoader" class="page-loader">
            <div class="flex flex-col items-center">
                <div class="loading-spinner"></div>
                <div class="mt-4 gradient-text text-lg font-semibold">Loading SecureVault...</div>
            </div>
        </div>

        <div class="min-h-screen">
            <!-- Navigation -->
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="glass-header sticky top-16 z-40 animate-slide-down">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="alert alert-success">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="alert alert-error">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="alert alert-warning">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('warning') }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="relative">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="glass-effect border-t border-yellow-400/20 mt-16">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex items-center space-x-4 mb-4 md:mb-0">
                            <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center animate-glow">
                                <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="gradient-text font-bold">{{ config('app.name', 'SecureVault') }}</div>
                                <div class="text-gray-400 text-sm">Premium Credential Management</div>
                            </div>
                        </div>
                        <div class="text-gray-400 text-sm text-center md:text-right">
                            <div>&copy; {{ date('Y') }} {{ config('app.name', 'SecureVault') }}. All rights reserved.</div>
                            <div class="mt-1">Secured with enterprise-grade encryption</div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Notification Container -->
        <div id="notification-container" class="fixed top-24 right-4 z-50 space-y-4">
            <!-- Dynamic notifications will be inserted here -->
        </div>

        <!-- FIX: Enhanced Global JavaScript with User Management Support -->
        <script>
            // FIX: Global Laravel configuration and CSRF setup
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}',
                user: @if(auth()->check()) {
                    id: {{ auth()->user()->id }},
                    name: '{{ auth()->user()->name }}',
                    email: '{{ auth()->user()->email }}',
                    role: '{{ auth()->user()->role }}',
                    is_active: {{ auth()->user()->is_active ? 'true' : 'false' }}
                } @else null @endif,
                appUrl: '{{ config('app.url') }}',
                appName: '{{ config('app.name') }}'
            };

            // FIX: Enhanced CSRF token retrieval
            function getCSRFToken() {
                return window.Laravel?.csrfToken || 
                       document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                       document.querySelector('input[name="_token"]')?.value;
            }

            // FIX: Global App utilities for user management
            window.App = {
                // Enhanced alert system
                showAlert: function(message, type = 'success', duration = 5000) {
                    const container = document.getElementById('notification-container');
                    if (!container) return;

                    const alertDiv = document.createElement('div');
                    const colors = {
                        success: 'bg-green-600 border-green-500',
                        error: 'bg-red-600 border-red-500',
                        warning: 'bg-yellow-600 border-yellow-500',
                        info: 'bg-blue-600 border-blue-500'
                    };
                    
                    alertDiv.className = `${colors[type] || colors.info} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 opacity-0 translate-x-full border`;
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
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/80 hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    container.appendChild(alertDiv);
                    
                    setTimeout(() => {
                        alertDiv.classList.remove('opacity-0', 'translate-x-full');
                    }, 100);
                    
                    if (duration > 0) {
                        setTimeout(() => {
                            alertDiv.classList.add('opacity-0', 'translate-x-full');
                            setTimeout(() => alertDiv.remove(), 300);
                        }, duration);
                    }
                },

                // Get CSRF token
                getCsrfToken: getCSRFToken,

                // Check authentication status
                isAuthenticated: function() {
                    return window.Laravel?.user !== null;
                },

                // Check admin status
                isAdmin: function() {
                    return window.Laravel?.user?.role === 'admin';
                },

                // Enhanced fetch with CSRF
                fetch: async function(url, options = {}) {
                    const csrfToken = this.getCsrfToken();
                    const defaultOptions = {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                        },
                        credentials: 'same-origin'
                    };

                    const mergedOptions = {
                        ...defaultOptions,
                        ...options,
                        headers: {
                            ...defaultOptions.headers,
                            ...options.headers
                        }
                    };

                    return fetch(url, mergedOptions);
                }
            };

            // FIX: User Management Functions
            window.UserManagement = {
                // Toggle user status with enhanced error handling
                async toggleStatus(userId) {
                    const button = document.querySelector(`.status-toggle-${userId}`);
                    const statusText = button?.querySelector('.status-text');
                    const row = document.getElementById(`user-row-${userId}`);
                    
                    if (!button) {
                        window.App.showAlert('Error: User status button not found', 'error');
                        return;
                    }

                    // Prevent double-clicking
                    if (button.classList.contains('loading')) {
                        return;
                    }

                    // Add loading state
                    button.classList.add('loading');
                    button.disabled = true;
                    const originalText = statusText?.textContent || button.textContent;
                    if (statusText) {
                        statusText.textContent = 'Updating...';
                    } else {
                        button.textContent = 'Updating...';
                    }
                    
                    try {
                        const response = await window.App.fetch(`/admin/users/${userId}/toggle-status`, {
                            method: 'POST'
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }

                        const data = await response.json();

                        if (data.success) {
                            // Update button appearance
                            const newText = data.status_text || (data.is_active ? 'Active' : 'Inactive');
                            if (statusText) {
                                statusText.textContent = newText;
                            } else {
                                button.textContent = newText;
                            }
                            
                            button.className = `status-badge status-toggle-${userId} ${data.is_active ? 'status-active' : 'status-inactive'}`;
                            button.setAttribute('data-current-status', data.is_active ? 'true' : 'false');
                            button.title = `Click to ${data.is_active ? 'deactivate' : 'activate'} user`;
                            
                            // Show success message
                            window.App.showAlert(data.message, 'success');
                            
                            // Add visual feedback
                            if (row) {
                                row.style.background = 'rgba(34, 197, 94, 0.1)';
                                row.style.transform = 'scale(1.02)';
                                setTimeout(() => {
                                    row.style.background = '';
                                    row.style.transform = '';
                                }, 2000);
                            }
                        } else {
                            throw new Error(data.error || 'Unknown error occurred');
                        }
                    } catch (error) {
                        console.error('Toggle status error:', error);
                        let errorMessage = 'An error occurred while updating user status';
                        
                        if (error.message.includes('HTTP 401')) {
                            errorMessage = 'Session expired. Please refresh and try again.';
                        } else if (error.message.includes('HTTP 403')) {
                            errorMessage = 'You do not have permission to perform this action.';
                        } else if (error.message.includes('HTTP 419')) {
                            errorMessage = 'Session expired. Please refresh and try again.';
                        } else if (error.message !== 'An error occurred while updating user status') {
                            errorMessage = error.message;
                        }
                        
                        window.App.showAlert(errorMessage, 'error');
                        
                        // Restore original text
                        if (statusText) {
                            statusText.textContent = originalText;
                        } else {
                            button.textContent = originalText;
                        }
                    } finally {
                        // Remove loading state
                        button.classList.remove('loading');
                        button.disabled = false;
                    }
                },

                // Enhanced checkbox toggle for forms
                toggleCheckbox(checkboxId) {
                    const checkbox = document.getElementById(checkboxId);
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        
                        // Trigger change event for any listeners
                        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        // Update any status indicators
                        this.updateStatusIndicator(checkboxId);
                    }
                },

                // Update status indicator for checkboxes
                updateStatusIndicator(checkboxId) {
                    const checkbox = document.getElementById(checkboxId);
                    const indicator = document.getElementById('status-indicator');
                    
                    if (checkbox && indicator) {
                        if (checkbox.checked) {
                            indicator.textContent = 'Active';
                            indicator.className = 'checkbox-status-indicator status-active-indicator';
                        } else {
                            indicator.textContent = 'Inactive';
                            indicator.className = 'checkbox-status-indicator status-inactive-indicator';
                        }
                    }
                }
            };

            // Make functions globally available
            window.toggleUserStatus = window.UserManagement.toggleStatus;
            window.toggleCheckbox = window.UserManagement.toggleCheckbox;
            window.updateStatusIndicator = window.UserManagement.updateStatusIndicator;

            document.addEventListener('DOMContentLoaded', function() {
                // Setup CSRF token for all requests
                const token = getCSRFToken();
                if (token) {
                    // Setup for fetch API
                    const originalFetch = window.fetch;
                    window.fetch = function(url, options = {}) {
                        if (!options.headers) options.headers = {};
                        if (!options.headers['X-CSRF-TOKEN']) {
                            options.headers['X-CSRF-TOKEN'] = token;
                        }
                        if (!options.headers['X-Requested-With']) {
                            options.headers['X-Requested-With'] = 'XMLHttpRequest';
                        }
                        return originalFetch(url, options);
                    };

                    // Setup for jQuery if available
                    if (typeof $ !== 'undefined') {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                    }

                    // Setup for Axios if available
                    if (typeof axios !== 'undefined') {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                    }
                }

                // Hide page loader
                setTimeout(() => {
                    const loader = document.getElementById('pageLoader');
                    if (loader) {
                        loader.classList.add('loader-hidden');
                        setTimeout(() => loader.remove(), 500);
                    }
                }, 800);

                // Auto-hide flash messages
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        alert.style.transition = 'all 0.5s ease-out';
                        alert.style.transform = 'translateY(-20px)';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 5000);

                // Smooth scrolling for anchor links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });

                // Add ripple effect to buttons
                document.querySelectorAll('button, .btn-primary, .btn-secondary').forEach(button => {
                    button.addEventListener('click', function(e) {
                        const ripple = document.createElement('span');
                        const rect = this.getBoundingClientRect();
                        const size = Math.max(rect.width, rect.height);
                        const x = e.clientX - rect.left - size / 2;
                        const y = e.clientY - rect.top - size / 2;
                        
                        ripple.style.cssText = `
                            position: absolute;
                            width: ${size}px;
                            height: ${size}px;
                            left: ${x}px;
                            top: ${y}px;
                            border-radius: 50%;
                            background: rgba(251, 191, 36, 0.3);
                            animation: ripple-animation 1s ease-out;
                            pointer-events: none;
                        `;
                        
                        this.style.position = 'relative';
                        this.style.overflow = 'hidden';
                        this.appendChild(ripple);
                        
                        setTimeout(() => ripple.remove(), 1000);
                    });
                });

                // Add intersection observer for animations
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, observerOptions);

                // Observe elements for animation
                document.querySelectorAll('.glass-effect, .card-hover').forEach(el => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'all 0.6s ease-out';
                    observer.observe(el);
                });

                // Global error handling
                window.addEventListener('error', function(e) {
                    console.error('Global error:', e.error);
                });

                window.addEventListener('unhandledrejection', function(e) {
                    console.error('Unhandled promise rejection:', e.reason);
                });

                // Connection monitoring
                window.addEventListener('online', function() {
                    window.App.showAlert('Connection restored', 'success', 3000);
                });

                window.addEventListener('offline', function() {
                    window.App.showAlert('Connection lost. Please check your internet connection.', 'warning', 0);
                });
            });

            // Custom CSS animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple-animation {
                    from {
                        transform: scale(0);
                        opacity: 1;
                    }
                    to {
                        transform: scale(1);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // Global notification function
            window.showNotification = function(message, type = 'info') {
                window.App.showAlert(message, type);
            };

            // OTP Status checker
            function otpStatus() {
                return {
                    hasValidSession: false,
                    
                    async checkStatus() {
                        try {
                            const response = await window.App.fetch('/otp/status');
                            
                            if (response.ok) {
                                const data = await response.json();
                                this.hasValidSession = data.has_valid_session;
                            }
                        } catch (error) {
                            console.log('Failed to check OTP status:', error);
                        }
                    },
                    
                    init() {
                        // Check status every 30 seconds
                        setInterval(() => {
                            this.checkStatus();
                        }, 30000);
                    }
                }
            }

            // Notification Bell functionality
            function notificationBell() {
                return {
                    isOpen: false,
                    notifications: [],
                    unreadCount: 0,
                    
                    init() {
                        this.fetchNotifications();
                        this.startPolling();
                    },
                    
                    toggleDropdown() {
                        this.isOpen = !this.isOpen;
                        if (this.isOpen) {
                            this.fetchNotifications();
                        }
                    },
                    
                    async fetchNotifications() {
                        try {
                            const response = await window.App.fetch('/api/notifications');
                            
                            if (response.ok) {
                                const data = await response.json();
                                this.notifications = data.notifications || [];
                                this.unreadCount = data.unread_count || 0;
                            }
                        } catch (error) {
                            console.error('Failed to fetch notifications:', error);
                        }
                    },
                    
                    async markAllAsRead() {
                        try {
                            const response = await window.App.fetch('/api/notifications/mark-all-read', {
                                method: 'POST'
                            });
                            
                            if (response.ok) {
                                this.notifications.forEach(notification => {
                                    notification.read_at = new Date().toISOString();
                                });
                                this.unreadCount = 0;
                            }
                        } catch (error) {
                            console.error('Failed to mark notifications as read:', error);
                        }
                    },
                    
                    async markAsRead(notificationId) {
                        try {
                            const response = await window.App.fetch(`/api/notifications/${notificationId}/read`, {
                                method: 'POST'
                            });
                            
                            if (response.ok) {
                                const notification = this.notifications.find(n => n.id === notificationId);
                                if (notification && !notification.read_at) {
                                    notification.read_at = new Date().toISOString();
                                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                                }
                            }
                        } catch (error) {
                            console.error('Failed to mark notification as read:', error);
                        }
                    },
                    
                    handleNotificationClick(notification) {
                        if (!notification.read_at) {
                            this.markAsRead(notification.id);
                        }
                        
                        if (notification.data.action_url) {
                            window.location.href = notification.data.action_url;
                        }
                    },
                    
                    formatTime(timestamp) {
                        const date = new Date(timestamp);
                        const now = new Date();
                        const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));
                        
                        if (diffInHours < 1) {
                            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
                            return diffInMinutes < 1 ? 'Just now' : `${diffInMinutes}m ago`;
                        } else if (diffInHours < 24) {
                            return `${diffInHours}h ago`;
                        } else {
                            return date.toLocaleDateString();
                        }
                    },
                    
                    startPolling() {
                        // Poll for new notifications every 30 seconds
                        setInterval(() => {
                            this.fetchNotifications();
                        }, 30000);
                    }
                }
            }

            // Make functions available globally for Alpine.js
            window.otpStatus = otpStatus;
            window.notificationBell = notificationBell;
        </script>
    </body>
</html>