{{-- File: resources/views/credentials/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('Add New Credential') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Secure your digital assets') }}</p>
            </div>
            <a href="{{ route('credentials.index') }}" 
               class="bg-transparent border-2 border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-black px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>{{ __('Back to List') }}</span>
            </a>
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
        
        .form-input {
            background: rgba(26, 26, 26, 0.8);
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
        
        .form-label {
            color: #ffffff;
            font-weight: 600;
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

        .btn-primary {
            background: linear-gradient(135deg, #ffdd00, #ffa500);
            color: #000000;
            border: none;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 221, 0, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 221, 0, 0.6);
            background: linear-gradient(135deg, #ffffff, #ffdd00);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .strength-indicator {
            height: 8px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 0.5rem;
            padding: 0.5rem;
            margin-top: 0.5rem;
            color: #f87171;
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
            <div class="glass-card rounded-xl overflow-hidden animate-fade-in">
                <form method="POST" action="{{ route('credentials.store') }}" x-data="credentialForm()" class="p-8 space-y-8">
                    @csrf

                    <!-- Category -->
                    <div class="space-y-2">
                        <label for="category_id" class="block text-sm font-medium form-label flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            {{ __('Category') }} *
                        </label>
                        <select id="category_id" name="category_id" required class="w-full rounded-lg form-input px-4 py-3">
                            <option value="">{{ __('Select a category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium form-label flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ __('Name') }} *
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                               class="w-full rounded-lg form-input px-4 py-3"
                               placeholder="e.g., Company Gmail Account">
                        @error('name')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username and Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="username" class="block text-sm font-medium form-label flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Username') }}
                            </label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" 
                                   class="w-full rounded-lg form-input px-4 py-3" placeholder="Enter username">
                            @error('username')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium form-label flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                                {{ __('Email') }}
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                   class="w-full rounded-lg form-input px-4 py-3" placeholder="Enter email address">
                            @error('email')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium form-label flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ __('Password') }} *
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   x-model="password"
                                   @input="calculateStrength"
                                   required 
                                   class="w-full rounded-lg form-input px-4 py-3 pr-12"
                                   placeholder="Enter a strong password">
                            <button type="button" @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-yellow-400 hover:text-yellow-300 transition-colors">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Password Strength Indicator -->
                        <div x-show="password.length > 0" class="mt-3 space-y-2" x-transition>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-300">{{ __('Password Strength:') }}</span>
                                <span class="text-sm font-medium" 
                                      :class="getStrengthTextColor()" 
                                      x-text="getStrengthText()"></span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="strength-indicator h-full rounded-full transition-all duration-300" 
                                     :class="getStrengthColor()"
                                     :style="'width: ' + (strength * 20) + '%'"></div>
                            </div>
                        </div>
                        
                        @error('password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website URL -->
                    <div class="space-y-2">
                        <label for="website_url" class="block text-sm font-medium form-label flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            {{ __('Website URL') }}
                        </label>
                        <input type="url" id="website_url" name="website_url" value="{{ old('website_url') }}" 
                               class="w-full rounded-lg form-input px-4 py-3"
                               placeholder="https://example.com">
                        @error('website_url')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Expiration -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="expires_at" class="block text-sm font-medium form-label flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('Expires On') }}
                            </label>
                            <input type="date" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" 
                                   :disabled="neverExpires"
                                   class="w-full rounded-lg form-input px-4 py-3 disabled:opacity-50">
                            @error('expires_at')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-end">
                            <label class="flex items-center p-3 rounded-lg bg-yellow-400/10 border border-yellow-400/20 cursor-pointer hover:bg-yellow-400/20 transition-colors">
                                <!-- Custom Checkbox with visible check icon -->
                                <div class="relative">
                                    <input type="checkbox" name="password_never_expires" value="1" 
                                           x-model="neverExpires"
                                           {{ old('password_never_expires') ? 'checked' : '' }}
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
                                <span class="ml-3 text-sm text-white font-medium">{{ __('Password never expires') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Backup Codes -->
                    <div class="space-y-4">
                        <label class="block text-sm font-medium form-label flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            {{ __('Backup Codes') }}
                        </label>
                        <div x-data="{ codes: [''] }" class="space-y-3">
                            <template x-for="(code, index) in codes" :key="index">
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           :name="'backup_codes[' + index + ']'"
                                           x-model="codes[index]"
                                           placeholder="Enter backup code"
                                           class="flex-1 rounded-lg form-input px-4 py-3">
                                    <button type="button" 
                                            @click="codes.splice(index, 1)"
                                            x-show="codes.length > 1"
                                            class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" 
                                    @click="codes.push('')"
                                    class="text-yellow-400 hover:text-yellow-300 text-sm font-medium flex items-center space-x-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>{{ __('Add another backup code') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <label for="notes" class="block text-sm font-medium form-label flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ __('Notes') }}
                        </label>
                        <textarea id="notes" name="notes" rows="4" 
                                  class="w-full rounded-lg form-input px-4 py-3 resize-none"
                                  placeholder="Additional notes or information...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 pt-8 border-t border-yellow-400/20">
                        <a href="{{ route('credentials.index') }}" 
                           class="btn-secondary px-8 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>{{ __('Cancel') }}</span>
                        </a>
                        <button type="submit" 
                                class="btn-primary px-8 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ __('Save Credential') }}</span>
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
                neverExpires: false,
                
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
                            return 'bg-gray-200';
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
                            return 'text-gray-400';
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