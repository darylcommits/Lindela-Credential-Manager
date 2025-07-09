{{-- File: resources/views/otp/verify.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('Verify OTP') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Enter your 6-digit verification code') }}</p>
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

        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes slideUp {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .verification-icon-container {
            background: rgba(34, 197, 94, 0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border: 2px solid rgba(34, 197, 94, 0.3);
        }

        .verification-icon {
            color: #22c55e;
            width: 40px;
            height: 40px;
        }

        .status-card {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 32px;
        }

        .status-icon {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .status-content {
            color: #6ee7b7;
        }

        .status-title {
            color: #22c55e;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .status-label {
            color: #10b981;
            font-weight: 600;
        }

        .otp-input {
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid rgba(255, 221, 0, 0.3);
            color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5rem;
            transition: all 0.3s ease;
        }

        .otp-input:focus {
            outline: none;
            border-color: #ffdd00;
            background: rgba(255, 221, 0, 0.1);
            box-shadow: 0 0 20px rgba(255, 221, 0, 0.3);
        }

        .otp-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
            letter-spacing: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ffdd00, #f59e0b);
            color: #000;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            transform: scale(1.05);
        }

        .btn-primary:disabled {
            background: rgba(75, 85, 99, 0.5);
            color: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
            transform: none;
        }

        .section-title {
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 16px;
        }

        .section-subtitle {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 32px;
        }

        .help-text {
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            font-size: 0.875rem;
        }

        .help-link {
            color: #60a5fa;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .help-link:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        .countdown-timer {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 8px;
        }

        .error-text {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 8px;
            text-align: center;
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl animate-fade-in">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <div class="verification-icon-container">
                            <svg class="verification-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="section-title">Enter OTP Code</h3>
                        <p class="section-subtitle">
                            Your OTP request has been approved. Enter the 6-digit code sent to your email.
                        </p>
                    </div>

                    <!-- OTP Status -->
                    <div class="status-card">
                        <div class="flex items-start">
                            <div class="status-icon mr-4">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="status-title">OTP Request Approved</h4>
                                <div class="status-content text-sm space-y-2">
                                    <div>
                                        <span class="status-label">Purpose:</span>
                                        <span class="ml-2">{{ ucfirst(str_replace('_', ' ', $validOtpRequest->purpose)) }}</span>
                                    </div>
                                    <div>
                                        <span class="status-label">Time remaining:</span>
                                        <span class="ml-2">{{ $validOtpRequest->time_remaining }}</span>
                                        <div class="countdown-timer" id="countdown">
                                            <span id="timer-display">{{ $validOtpRequest->time_remaining }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('otp.confirm') }}" x-data="otpForm()" class="space-y-8">
                        @csrf

                        <!-- OTP Code Input -->
                        <div class="space-y-4">
                            <label for="otp_code" class="block text-sm font-semibold text-yellow-400 text-center">6-Digit OTP Code</label>
                            <input type="text" 
                                   id="otp_code" 
                                   name="otp_code" 
                                   x-model="otpCode"
                                   @input="formatOtp"
                                   maxlength="6" 
                                   required
                                   class="w-full rounded-xl px-6 py-4 otp-input"
                                   placeholder="000000"
                                   autocomplete="off"
                                   autofocus>
                            @error('otp_code')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    :disabled="otpCode.length !== 6"
                                    class="w-full px-6 py-4 rounded-xl font-semibold transition-all duration-300 btn-primary flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Verify OTP & Access Credentials</span>
                            </button>
                        </div>
                    </form>

                    <!-- Help Text -->
                    <div class="mt-8 text-center">
                        <p class="help-text">
                            Didn't receive the code? Check your spam folder or 
                            <a href="{{ route('otp.request') }}" class="help-link">
                                request a new OTP
                            </a>
                        </p>
                        <p class="help-text mt-2">
                            <strong>Note:</strong> After successful verification, you'll be redirected to the credentials manager.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function otpForm() {
            return {
                otpCode: '',
                
                formatOtp() {
                    // Remove non-numeric characters
                    this.otpCode = this.otpCode.replace(/\D/g, '');
                    
                    // Limit to 6 digits
                    if (this.otpCode.length > 6) {
                        this.otpCode = this.otpCode.substring(0, 6);
                    }
                }
            }
        }

        // Countdown timer functionality
        document.addEventListener('DOMContentLoaded', function() {
            const expiresAt = new Date('{{ $validOtpRequest->expires_at->toISOString() }}');
            const timerDisplay = document.getElementById('timer-display');
            
            function updateCountdown() {
                const now = new Date();
                const timeLeft = expiresAt - now;
                
                if (timeLeft <= 0) {
                    timerDisplay.textContent = 'Expired';
                    timerDisplay.parentElement.classList.add('animate-pulse-slow');
                    // Optionally redirect to request new OTP
                    setTimeout(() => {
                        window.location.href = '{{ route("otp.request") }}';
                    }, 2000);
                    return;
                }
                
                const minutes = Math.floor(timeLeft / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
            
            // Update immediately and then every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        });

        // Auto-focus on page load
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp_code');
            if (otpInput) {
                otpInput.focus();
            }
        });
    </script>
</x-app-layout>