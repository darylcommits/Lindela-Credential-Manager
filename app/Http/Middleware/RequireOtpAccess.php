<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RequireOtpAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Admin users bypass OTP requirement
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if this is an AJAX request for OTP status
        if ($request->ajax() && $request->is('api/otp/status')) {
            return $next($request);
        }

        // Skip OTP check for OTP-related routes
        if ($request->routeIs(['otp.*', 'dashboard'])) {
            return $next($request);
        }

        // Check if user has valid OTP access
        if ($user->hasValidOtp()) {
            return $next($request); // Allow access
        }

        // Check if user has approved OTP waiting for verification
        $approvedRequest = $user->otpRequests()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if ($approvedRequest) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'OTP verification required',
                    'redirect' => route('otp.verify'),
                    'message' => 'Please verify your approved OTP code to access credentials.'
                ], 403);
            }
            
            return redirect()->route('otp.verify')
                ->with('info', 'Please verify your approved OTP code to access credentials.');
        }

        // Check if user has pending request
        $pendingRequest = $user->otpRequests()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($pendingRequest) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'OTP approval pending',
                    'redirect' => route('otp.request'),
                    'message' => 'Your OTP request is pending admin approval.'
                ], 403);
            }
            
            return redirect()->route('otp.request')
                ->with('info', 'Your OTP request is pending admin approval.');
        }

        // No OTP request exists, redirect to request form
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'OTP required',
                'redirect' => route('otp.request'),
                'message' => 'You need to request OTP access before viewing credentials.'
            ], 403);
        }

        return redirect()->route('otp.request')
            ->with('warning', 'You need to request OTP access before viewing credentials.');
    }
}