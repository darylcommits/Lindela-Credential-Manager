{{-- File: resources/views/admin/users/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('Create New User') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Add a new user to the system') }}</p>
            </div>
            <div class="flex items-center space-x-4">
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

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: #fbbf24;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input, .form-select {
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            color: #ffffff;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #ffdd00;
            background: rgba(255, 221, 0, 0.1);
            box-shadow: 0 0 20px rgba(255, 221, 0, 0.2);
            transform: scale(1.02);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select option {
            background: #1a1a1a;
            color: #ffffff;
        }

        .form-error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-help {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.875rem;
            margin-top: 8px;
            font-style: italic;
        }

        /* FIXED: Consistent checkbox styling with edit form */
        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid rgba(255, 221, 0, 0.3);
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
        }

        .checkbox-container:hover {
            background: rgba(255, 221, 0, 0.05);
            border-color: rgba(255, 221, 0, 0.5);
        }

        .checkbox-container input[type="checkbox"] {
            width: 24px;
            height: 24px;
            margin: 0;
            padding: 0;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: rgba(0, 0, 0, 0.8);
            border: 3px solid rgba(255, 221, 0, 0.5);
            border-radius: 6px;
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .checkbox-container input[type="checkbox"]:checked {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-color: #fbbf24;
            transform: scale(1.1);
        }

        /* FIXED: Better visible checkmark */
        .checkbox-container input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            animation: checkmark 0.3s ease-in-out;
        }

        @keyframes checkmark {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0);
            }
            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.2);
            }
            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .checkbox-container input[type="checkbox"]:hover {
            border-color: rgba(255, 221, 0, 0.8);
            box-shadow: 0 0 15px rgba(255, 221, 0, 0.3);
        }

        .checkbox-container input[type="checkbox"]:focus {
            outline: 2px solid #fbbf24;
            outline-offset: 2px;
        }

        .checkbox-label {
            color: #ffffff;
            font-weight: 500;
            cursor: pointer;
            flex: 1;
            line-height: 1.5;
        }

        .checkbox-status-indicator {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
            display: inline-block;
        }

        .status-active-indicator {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-inactive-indicator {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ffdd00, #f59e0b);
            color: #000;
            font-weight: 600;
            padding: 16px 32px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 221, 0, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: rgba(75, 85, 99, 0.8);
            color: #ffffff;
            border: 1px solid rgba(255, 221, 0, 0.3);
            font-weight: 600;
            padding: 16px 32px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
            transform: scale(1.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .form-grid .full-width {
            grid-column: 1 / -1;
        }

        .password-input-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #fbbf24;
        }

        .role-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 8px;
            padding: 12px 16px;
            color: #93c5fd;
            font-size: 0.875rem;
            margin-top: 8px;
        }

        .success-animation {
            animation: successPulse 0.6s ease-out;
        }

        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(255, 221, 0, 0.3);
            border-top: 4px solid #ffdd00;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8" id="createUserForm">
                @csrf
                
                <div class="glass-card rounded-2xl p-8 animate-fade-in">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-white mb-2">User Information</h3>
                        <p class="text-gray-400">Enter the basic information for the new user account.</p>
                    </div>

                    <div class="form-grid">
                        <!-- Name -->
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="Enter full name"
                                   class="form-input @error('name') border-red-500 @enderror"
                                   required
                                   autocomplete="name">
                            @error('name')
                                <div class="form-error">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="user@example.com"
                                   class="form-input @error('email') border-red-500 @enderror"
                                   required
                                   autocomplete="email">
                            @error('email')
                                <div class="form-error">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-help">
                                The user will receive account details at this email address.
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password *</label>
                            <div class="password-input-container">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter secure password"
                                       class="form-input @error('password') border-red-500 @enderror"
                                       required
                                       autocomplete="new-password"
                                       minlength="8">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <div class="form-error">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-help">
                                Password must be at least 8 characters long and contain a mix of letters, numbers, and symbols.
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <div class="password-input-container">
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm password"
                                       class="form-input"
                                       required
                                       autocomplete="new-password"
                                       minlength="8">
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-help">
                                Re-enter the password to confirm.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-8 animate-fade-in">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-white mb-2">Role & Permissions</h3>
                        <p class="text-gray-400">Set the user's role and account status.</p>
                    </div>

                    <div class="form-grid">
                        <!-- Role -->
                        <div class="form-group">
                            <label for="role" class="form-label">User Role *</label>
                            <select id="role" 
                                    name="role" 
                                    class="form-select @error('role') border-red-500 @enderror"
                                    required
                                    onchange="updateRoleInfo()">
                                <option value="">Select a role...</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="it_staff" {{ old('role') === 'it_staff' ? 'selected' : '' }}>IT Staff</option>
                            </select>
                            @error('role')
                                <div class="form-error">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div id="role-info" class="role-info" style="display: none;">
                                <!-- Role information will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Account Status - FIXED: Consistent checkbox styling -->
                        <div class="form-group full-width">
                            <label class="form-label">Account Status</label>
                            <div class="checkbox-container">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', '1') ? 'checked' : '' }}
                                       onchange="updateStatusIndicator()">
                                <label for="is_active" class="checkbox-label">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-semibold text-lg">Account Active</span>
                                            <div class="text-sm text-gray-400 mt-1">
                                                When enabled, the user can log in and access the system immediately.
                                            </div>
                                        </div>
                                        <div id="status-indicator" class="checkbox-status-indicator status-active-indicator">
                                            Active
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="form-help mt-2">
                                <strong>Check the box above to create an active account, uncheck to create an inactive account</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="glass-card rounded-2xl p-8 animate-fade-in">
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>Cancel</span>
                        </a>
                        <button type="submit" class="btn-primary" id="submitBtn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Create User</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <div class="text-white text-lg">Creating user account...</div>
        </div>
    </div>

    <script>
        // Role information
        const roleInfo = {
            admin: {
                title: 'Administrator',
                description: 'Full system access including user management, system configuration, and all credential operations. Admins do not require OTP approval.',
                permissions: ['Full user management', 'System configuration', 'Credential management', 'Security logs', 'Reports and analytics']
            },
            it_staff: {
                title: 'IT Staff',
                description: 'Limited access to credential management with OTP requirement. Cannot manage users or system settings.',
                permissions: ['View credentials (with OTP)', 'Request OTP access', 'Basic reporting', 'Profile management']
            }
        };

        // FIXED: Status indicator update function
        function updateStatusIndicator() {
            const checkbox = document.getElementById('is_active');
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

        // Update role information display
        function updateRoleInfo() {
            const roleSelect = document.getElementById('role');
            const roleInfoDiv = document.getElementById('role-info');
            const selectedRole = roleSelect.value;

            if (selectedRole && roleInfo[selectedRole]) {
                const info = roleInfo[selectedRole];
                roleInfoDiv.innerHTML = `
                    <div class="font-semibold mb-2">${info.title}</div>
                    <div class="mb-3">${info.description}</div>
                    <div class="font-medium mb-1">Permissions:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        ${info.permissions.map(permission => `<li>${permission}</li>`).join('')}
                    </ul>
                `;
                roleInfoDiv.style.display = 'block';
            } else {
                roleInfoDiv.style.display = 'none';
            }
        }

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.464 6.464m3.414 3.414l-1.414-1.414M21 3l-18 18"/>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        // Password strength validation
        function validatePasswordStrength() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            
            let strength = 0;
            let feedback = [];

            // Length check
            if (password.length >= 8) strength++;
            else feedback.push('At least 8 characters');

            // Character variety checks
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('Lowercase letters');

            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('Uppercase letters');

            if (/[0-9]/.test(password)) strength++;
            else feedback.push('Numbers');

            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('Special characters');

            // Password match check
            if (password && confirmation && password !== confirmation) {
                feedback.push('Passwords must match');
            }

            return { strength, feedback, isValid: strength >= 3 && password === confirmation };
        }

        // Real-time password validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submitBtn');

            function checkPasswords() {
                const validation = validatePasswordStrength();
                
                // Update submit button state
                if (validation.isValid) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50');
                }
            }

            passwordInput.addEventListener('input', checkPasswords);
            confirmInput.addEventListener('input', checkPasswords);

            // Initial check
            checkPasswords();
        });

        // Form submission with loading
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            const submitBtn = document.getElementById('submitBtn');
            
            // Show loading overlay
            loadingOverlay.classList.add('active');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Creating...</span>
            `;
        });

        // Prevent multiple submissions
        let isSubmitting = false;
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
        });

        // Initialize role info on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateRoleInfo();
            updateStatusIndicator();
        });

        // Auto-focus first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('name').focus();
        });

        // Form keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + Enter to submit
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                const form = document.getElementById('createUserForm');
                if (!isSubmitting && form.checkValidity()) {
                    form.submit();
                }
            }
            
            // Escape to cancel
            if (e.key === 'Escape') {
                e.preventDefault();
                if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                    window.location.href = "{{ route('admin.users.index') }}";
                }
            }
        });
    </script>
</x-app-layout>