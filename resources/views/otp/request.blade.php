{{-- File: resources/views/otp/request.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('Request OTP') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Request secure access to credentials') }}</p>
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

        .pending-request-card {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .pending-icon {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 24px;
            flex-shrink: 0;
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

        .request-icon-container {
            background: rgba(255, 221, 0, 0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border: 2px solid rgba(255, 221, 0, 0.3);
        }

        .request-icon {
            color: #ffdd00;
            width: 40px;
            height: 40px;
        }

        .info-card {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 16px;
            padding: 20px;
            margin-top: 32px;
        }

        .info-icon {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-content {
            color: #93c5fd;
        }

        .info-title {
            color: #60a5fa;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .pending-details {
            color: #fbbf24;
        }

        .pending-label {
            color: #f59e0b;
            font-weight: 600;
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
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl animate-fade-in">
                <div class="p-8">
                    @if(isset($pendingRequest))
                        <!-- Pending Request Status -->
                        <div class="pending-request-card animate-pulse-slow">
                            <div class="flex items-start">
                                <div class="pending-icon">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-yellow-400 mb-4">
                                        OTP Request Pending
                                    </h3>
                                    <div class="space-y-3 text-sm pending-details">
                                        <p>You have a pending OTP request that expires {{ $pendingRequest->time_remaining }}.</p>
                                        <div class="grid grid-cols-1 gap-2">
                                            <div>
                                                <span class="pending-label">Purpose:</span>
                                                <span class="ml-2">{{ ucfirst(str_replace('_', ' ', $pendingRequest->purpose)) }}</span>
                                            </div>
                                            <div>
                                                <span class="pending-label">Requested:</span>
                                                <span class="ml-2">{{ $pendingRequest->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Request Form -->
                        <div class="text-center mb-8">
                            <div class="request-icon-container">
                                <svg class="request-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2m0 0V7a2 2 0 012-2h4zm-8 9V9a2 2 0 112 2v6a2 2 0 11-2 2z" />
                                </svg>
                            </div>
                            <h3 class="section-title">Request OTP Access</h3>
                            <p class="section-subtitle">
                                You need administrator approval to access credentials. Submit your request below.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('otp.submit') }}" class="space-y-8">
                            @csrf

                            <!-- Purpose -->
                            <div class="field-group">
                                <label for="purpose" class="form-label">Purpose *</label>
                                <select id="purpose" name="purpose" required class="w-full rounded-xl px-4 py-3 form-input">
                                    <option value="">Select purpose</option>
                                    <option value="credential_access" {{ old('purpose') == 'credential_access' ? 'selected' : '' }}>
                                        Access Credentials
                                    </option>
                                    <option value="account_management" {{ old('purpose') == 'account_management' ? 'selected' : '' }}>
                                        Account Management
                                    </option>
                                </select>
                                @error('purpose')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="field-group">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea id="notes" name="notes" rows="4" 
                                          class="w-full rounded-xl px-4 py-3 form-input resize-none"
                                          placeholder="Provide additional context for your request...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" class="px-8 py-3 rounded-xl font-semibold transition-all duration-300 btn-primary flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    <span>Submit Request</span>
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Information -->
                    <div class="info-card">
                        <div class="flex items-start">
                            <div class="info-icon mr-4">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 info-content">
                                <h4 class="info-title">How it works:</h4>
                                <ul class="mt-2 space-y-2 text-sm">
                                    <li class="flex items-start">
                                        <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                        <span>Submit your OTP request with a valid purpose</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                        <span>Administrator will review and approve/deny your request</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                        <span>Once approved, you'll receive an OTP code via email</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                        <span>OTP codes expire after 5 minutes for security</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>