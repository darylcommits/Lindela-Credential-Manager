{{-- File: resources/views/credentials/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('Edit Credential') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Update your credential information') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('credentials.show', $credential) }}" 
                   class="bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>{{ __('View Details') }}</span>
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

        .form-input:disabled {
            background: rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 221, 0, 0.1);
            color: rgba(255, 255, 255, 0.5);
        }

        .form-label {
            color: #ffdd00;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .error-text {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 4px;
        }

        .strength-indicator {
            background: rgba(255, 221, 0, 0.1);
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .backup-code-item {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.2);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .backup-code-item:hover {
            border-color: rgba(255, 221, 0, 0.4);
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

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: #ef4444;
            transform: scale(1.1);
        }

        .btn-add {
            background: rgba(255, 221, 0, 0.1);
            color: #ffdd00;
            border: 1px solid rgba(255, 221, 0, 0.3);
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
        }

        .section-divider {
            border-color: rgba(255, 221, 0, 0.2);
            margin: 32px 0;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .toggle-button {
            background: rgba(255, 221, 0, 0.1);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffdd00;
            transition: all 0.3s ease;
        }

        .toggle-button:hover {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
            transform: scale(1.1);
        }

        .field-group {
            background: rgba(26, 26, 26, 0.4);
            border: 1px solid rgba(255, 221, 0, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .field-group:hover {
            border-color: rgba(255, 221, 0, 0.3);
        }

        .progress-bar {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            overflow: hidden;
            height: 8px;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            transition: all 0.5s ease;
            background: linear-gradient(90deg, #ef4444, #f59e0b, #22c55e);
        }

        /* Custom Checkbox Styles */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        .custom-checkbox:checked + .checkbox-icon {
            background-color: #fbbf24;
            border-color: #fbbf24;
        }
        
        .custom-checkbox:checked + .checkbox-icon .check-icon {
            opacity: 1;
            transform: scale(1);
        }
        
        .checkbox-icon {
            transition: all 0.2s ease;
        }
        
        .check-icon {
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.2s ease;
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl animate-fade-in">
                <form method="POST" action="{{ route('credentials.update', $credential) }}" x-data="credentialForm()" class="p-8 space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Category & Name Section -->
                    <div class="field-group">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <svg class="w-6 h-6 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Basic Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Category -->
                            <div>
                                <label for="category_id" class="form-label">Category *</label>
                                <select id="category_id" name="category_id" required class="w-full rounded-xl px-4 py-3 form-input">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $credential->category_id) == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $credential->name) }}" required 
                                       class="w-full rounded-xl px-4 py-3 form-input"
                                       placeholder="e.g., Company Gmail Account">
                                @error('name')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Credentials Section -->
                    <div class="field-group">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <svg class="w-6 h-6 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Login Credentials
                        </h3>

                        <!-- Username and Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" value="{{ old('username', $credential->username) }}" 
                                       class="w-full rounded-xl px-4 py-3 form-input" placeholder="Enter username">
                                @error('username')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $credential->email) }}" 
                                       class="w-full rounded-xl px-4 py-3 form-input" placeholder="Enter email address">
                                @error('email')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="form-label">Password *</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" 
                                       id="password" 
                                       name="password" 
                                       x-model="password"
                                       @input="calculateStrength"
                                       value="{{ old('password', $credential->password) }}"
                                       required 
                                       class="w-full rounded-xl px-4 py-3 pr-12 form-input font-mono"
                                       placeholder="Enter a strong password">
                                <button type="button" @click="showPassword = !showPassword" 
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center toggle-button rounded-r-xl">
                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Enhanced Password Strength Indicator -->
                            <div x-show="password.length > 0" class="strength-indicator mt-4" x-init="password = '{{ old('password', $credential->password) }}'; calculateStrength()">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-yellow-400">Password Strength</span>
                                    <span class="text-sm font-semibold" 
                                          :class="getStrengthTextColor()" 
                                          x-text="getStrengthText()"></span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" :style="'width: ' + (strength * 20) + '%'"></div>
                                </div>
                                <div class="flex space-x-1 mt-2">
                                    <template x-for="i in 5" :key="i">
                                        <div class="flex-1 strength-bar" 
                                             :class="i <= strength ? getStrengthColor() : 'bg-gray-600'"></div>
                                    </template>
                                </div>
                            </div>
                            
                            @error('password')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Website & Expiration Section -->
                    <div class="field-group">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <svg class="w-6 h-6 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Website & Expiration
                        </h3>

                        <!-- Website URL -->
                        <div class="mb-6">
                            <label for="website_url" class="form-label">Website URL</label>
                            <input type="url" id="website_url" name="website_url" value="{{ old('website_url', $credential->website_url) }}" 
                                   class="w-full rounded-xl px-4 py-3 form-input"
                                   placeholder="https://example.com">
                            @error('website_url')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Expiration -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="expires_at" class="form-label">Expires On</label>
                                <input type="date" id="expires_at" name="expires_at" 
                                       value="{{ old('expires_at', $credential->expires_at ? $credential->expires_at->format('Y-m-d') : '') }}" 
                                       :disabled="neverExpires"
                                       class="w-full rounded-xl px-4 py-3 form-input">
                                @error('expires_at')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-end">
                                <label class="flex items-center p-3 rounded-xl bg-yellow-400/10 border border-yellow-400/20 cursor-pointer hover:bg-yellow-400/20 transition-colors">
                                    <!-- Custom Checkbox with visible check icon -->
                                    <div class="relative">
                                        <input type="checkbox" name="password_never_expires" value="1" 
                                               x-model="neverExpires"
                                               {{ old('password_never_expires', $credential->password_never_expires) ? 'checked' : '' }}
                                               class="sr-only">
                                        <div class="w-5 h-5 rounded border-2 transition-all duration-200 flex items-center justify-center"
                                             :class="neverExpires ? 'bg-yellow-400 border-yellow-400' : 'border-yellow-400/50 bg-transparent hover:border-yellow-400'">
                                            <svg x-show="neverExpires" 
                                                 class="w-3 h-3 text-black" 
                                                 fill="none" 
                                                 stroke="currentColor" 
                                                 viewBox="0 0 24 24"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-50"
                                                 x-transition:enter-end="opacity-100 scale-100">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-sm text-gray-300 font-medium">Password never expires</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Codes Section -->
                    <div class="field-group">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <svg class="w-6 h-6 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Backup Codes
                        </h3>

                        <div x-data="{ codes: @js(old('backup_codes', $credential->backup_codes ?: [''])) }">
                            <template x-for="(code, index) in codes" :key="index">
                                <div class="backup-code-item">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-400/20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-yellow-400" x-text="index + 1"></span>
                                        </div>
                                        <input type="text" 
                                               :name="'backup_codes[' + index + ']'"
                                               x-model="codes[index]"
                                               placeholder="Enter backup code"
                                               class="flex-1 rounded-lg px-3 py-2 form-input font-mono text-sm">
                                        <button type="button" 
                                                @click="codes.splice(index, 1)"
                                                x-show="codes.length > 1"
                                                class="p-2 rounded-lg btn-danger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" 
                                    @click="codes.push('')"
                                    class="w-full py-3 px-4 rounded-xl btn-add font-medium flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>Add another backup code</span>
                            </button>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="field-group">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <svg class="w-6 h-6 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Additional Notes
                        </h3>

                        <div>
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" name="notes" rows="4" 
                                      class="w-full rounded-xl px-4 py-3 form-input resize-none"
                                      placeholder="Additional notes or information...">{{ old('notes', $credential->notes) }}</textarea>
                            @error('notes')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 pt-8 border-t section-divider">
                        <a href="{{ route('credentials.show', $credential) }}" 
                           class="px-8 py-3 rounded-xl font-semibold transition-all duration-300 btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 rounded-xl font-semibold transition-all duration-300 btn-primary flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Update Credential</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function credentialForm() {
            return {
                showPassword: false,
                password: '',
                strength: 0,
                neverExpires: {{ $credential->password_never_expires ? 'true' : 'false' }},
                
                calculateStrength() {
                    let score = 0;
                    
                    // Length
                    if (this.password.length >= 8) score++;
                    if (this.password.length >= 12) score++;
                    
                    // Character types
                    if (/[a-z]/.test(this.password)) score++;
                    if (/[A-Z]/.test(this.password)) score++;
                    if (/[0-9]/.test(this.password)) score++;
                    if (/[^a-zA-Z0-9]/.test(this.password)) score++;
                    
                    this.strength = Math.min(score, 5);
                },
                
                getStrengthColor() {
                    switch (this.strength) {
                        case 1:
                        case 2:
                            return 'bg-red-500';
                        case 3:
                            return 'bg-yellow-500';
                        case 4:
                            return 'bg-blue-500';
                        case 5:
                            return 'bg-green-500';
                        default:
                            return 'bg-gray-600';
                    }
                },
                
                getStrengthTextColor() {
                    switch (this.strength) {
                        case 1:
                        case 2:
                            return 'text-red-400';
                        case 3:
                            return 'text-yellow-400';
                        case 4:
                            return 'text-blue-400';
                        case 5:
                            return 'text-green-400';
                        default:
                            return 'text-gray-500';
                    }
                },
                
                getStrengthText() {
                    switch (this.strength) {
                        case 1:
                        case 2:
                            return 'Weak';
                        case 3:
                            return 'Fair';
                        case 4:
                            return 'Good';
                        case 5:
                            return 'Strong';
                        default:
                            return '';
                    }
                }
            }
        }
    </script>
</x-app-layout>