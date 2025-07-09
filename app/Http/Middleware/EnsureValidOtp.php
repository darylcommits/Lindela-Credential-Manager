<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;

class EnsureValidOtp
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin users don't need OTP for basic operations
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has valid OTP
        if (!$user->hasValidOtp()) {
            return redirect()->route('otp.request')
                ->with('warning', 'You need to request and verify an OTP to access credentials.');
        }

        return $next($request);
    }
}