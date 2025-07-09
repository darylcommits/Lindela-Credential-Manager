<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OtpService;
use App\Models\OtpRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->middleware(['auth', 'user.active', 'track.activity']);
        $this->otpService = $otpService;
    }

    public function showRequestForm()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user already has a valid OTP
        if ($user->hasValidOtp()) {
            return redirect()->route('credentials.index')
                ->with('info', 'You already have a valid OTP and can access credentials.');
        }

        // Check if user has an approved OTP that needs verification
        $approvedOtp = $user->otpRequests()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if ($approvedOtp) {
            return redirect()->route('otp.verify')
                ->with('success', 'Your OTP request has been approved! Please verify your code to access credentials.');
        }

        // Check if user has a pending OTP request
        $pendingRequest = $user->otpRequests()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        return view('otp.request', compact('pendingRequest'));
    }

    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|in:credential_access,account_management',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        try {
            $otpRequest = $this->otpService->requestOtp(
                $user,
                $request->purpose,
                []
            );

            return back()->with([
                'success' => 'OTP request submitted successfully. Please wait for admin approval.',
                'info' => 'You will be notified via email once your request is reviewed by an administrator.'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showVerifyForm()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user already has a valid (used) OTP
        if ($user->hasValidOtp()) {
            return redirect()->route('credentials.index')
                ->with('info', 'You already have access to credentials.');
        }

        // Find approved OTP request that needs verification
        $validOtpRequest = $user->otpRequests()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if (!$validOtpRequest) {
            return redirect()->route('otp.request')
                ->with('warning', 'You need to request an OTP first or your previous OTP has expired.');
        }

        return view('otp.verify', compact('validOtpRequest'));
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        try {
            $otpRequest = $this->otpService->verifyOtp($user, $request->otp_code);

            // Redirect to credentials with success message
            return redirect()->route('credentials.index')->with([
                'success' => 'OTP verified successfully! You now have access to credentials.',
                'info' => 'Your OTP session will remain active for the configured duration.'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function status()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $validOtp = $this->otpService->getValidOtpForUser($user);
        $pendingRequest = $user->otpRequests()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
        
        $approvedRequest = $user->otpRequests()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        return response()->json([
            'hasValidOtp' => $user->hasValidOtp(),
            'hasPendingRequest' => $pendingRequest !== null,
            'hasApprovedRequest' => $approvedRequest !== null,
            'needsVerification' => $approvedRequest !== null && !$user->hasValidOtp(),
            'otpRequest' => $validOtp,
            'pendingRequest' => $pendingRequest,
            'approvedRequest' => $approvedRequest,
            'timeRemaining' => $validOtp ? $validOtp->time_remaining : null,
            'nextStep' => $this->getNextStep($user, $pendingRequest, $approvedRequest, $validOtp),
        ]);
    }

    private function getNextStep($user, $pendingRequest, $approvedRequest, $validOtp)
    {
        if ($user->hasValidOtp()) {
            return 'access_credentials';
        }
        
        if ($approvedRequest) {
            return 'verify_otp';
        }
        
        if ($pendingRequest) {
            return 'wait_for_approval';
        }
        
        return 'request_otp';
    }

    /**
     * Check if user needs OTP verification before accessing credentials
     */
    public function checkOtpAccess()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin users bypass OTP requirement
        if ($user->role === 'admin') {
            return null; // Allow access
        }

        // Check if user has valid OTP access
        if ($user->hasValidOtp()) {
            return null; // Allow access
        }

        // Check if user has approved OTP waiting for verification
        $approvedRequest = $user->otpRequests()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if ($approvedRequest) {
            return redirect()->route('otp.verify')
                ->with('info', 'Please verify your approved OTP code to access credentials.');
        }

        // Check if user has pending request
        $pendingRequest = $user->otpRequests()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($pendingRequest) {
            return redirect()->route('otp.request')
                ->with('info', 'Your OTP request is pending admin approval.');
        }

        // No OTP request exists, redirect to request form
        return redirect()->route('otp.request')
            ->with('warning', 'You need to request OTP access before viewing credentials.');
    }
}