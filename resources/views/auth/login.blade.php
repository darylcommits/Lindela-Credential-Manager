<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div style="background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 0.5rem; padding: 0.75rem; color: #fbbf24; margin-bottom: 1rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <div class="input-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <input 
                id="email" 
                class="form-input" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                autocomplete="username" 
                placeholder="Email" />
            @if ($errors->get('email'))
                <div style="color: #f87171; font-size: 0.875rem; margin-top: 0.5rem;">
                    {{ $errors->first('email') }}
                </div>
            @endif
        </div>

        <!-- Password -->
        <div class="form-group">
            <div class="input-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <input 
                id="password" 
                class="form-input"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
                placeholder="Password" />
            <div class="password-toggle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
            @if ($errors->get('password'))
                <div style="color: #f87171; font-size: 0.875rem; margin-top: 0.5rem;">
                    {{ $errors->first('password') }}
                </div>
            @endif
        </div>

        <!-- Forgot Password Link -->
        @if (Route::has('password.request'))
            <div class="forgot-password">
                <a href="{{ route('password.request') }}">
                    Forgot Your Password ?
                </a>
            </div>
        @endif

        <!-- Login Button -->
        <button type="submit" class="enter-button">
            <svg class="w-5 h-5 airplane-icon" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2 21L23 12L2 3V10L17 12L2 14V21Z"/>
            </svg>
            LOGIN
        </button>

        <!-- Remember Me (Hidden) -->
        <input type="hidden" name="remember" value="1">
    </form>
</x-guest-layout>