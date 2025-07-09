<?php

namespace App\Services;

use App\Models\OtpRequest;
use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpRequestMail;
use App\Mail\OtpApprovedMail;

class OtpService
{
    public function requestOtp(User $user, string $purpose, array $requestedResources = [])
    {
        // Check if user already has a pending OTP request
        $existingRequest = $user->otpRequests()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingRequest) {
            throw new \Exception('You already have a pending OTP request. Please wait for approval or expiration.');
        }

        // Check if user has an approved OTP waiting for verification
        $approvedRequest = $user->otpRequests()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if ($approvedRequest) {
            throw new \Exception('You have an approved OTP waiting for verification. Please verify your code first.');
        }

        // Generate OTP
        $otpCode = OtpRequest::generateOtp();

        // Create OTP request with 30 minutes expiration for approval
        $otpRequest = OtpRequest::create([
            'user_id' => $user->id,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'requested_resources' => $requestedResources,
            'expires_at' => now()->addMinutes(30), // 30 minutes for admin approval
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Send email notification to user
        try {
            Mail::to($user->email)->send(new OtpRequestMail($otpRequest));
        } catch (\Exception $e) {
            // Log email failure but don't fail the request
            SystemLog::logAction(
                'otp_email_failed',
                'otp',
                "Failed to send OTP request email to {$user->email}: {$e->getMessage()}",
                'warning',
                $user->id
            );
        }

        // Notify admins about the new request
        $this->notifyAdminsOfNewRequest($otpRequest);

        // Log the OTP request
        SystemLog::logAction(
            'otp_requested',
            'otp',
            "OTP requested for purpose: {$purpose}",
            'info',
            $user->id
        );

        return $otpRequest;
    }

    public function approveOtp(OtpRequest $otpRequest, User $approver, string $notes = null)
    {
        if ($otpRequest->status !== 'pending') {
            throw new \Exception('OTP request is no longer pending.');
        }

        if ($otpRequest->isExpired()) {
            $otpRequest->markAsExpired();
            throw new \Exception('OTP request has expired.');
        }

        // Update the OTP request with approval and extend expiration for verification
        $otpRequest->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'admin_notes' => $notes,
            'expires_at' => now()->addMinutes(5), // 5 minutes for user verification
        ]);

        // Send approval email to user with OTP code
        try {
            Mail::to($otpRequest->user->email)->send(new OtpApprovedMail($otpRequest));
        } catch (\Exception $e) {
            SystemLog::logAction(
                'otp_approval_email_failed',
                'otp',
                "Failed to send OTP approval email to {$otpRequest->user->email}: {$e->getMessage()}",
                'warning',
                $approver->id
            );
        }

        // Log the approval
        SystemLog::logAction(
            'otp_approved',
            'otp',
            "OTP approved for user {$otpRequest->user->name} by {$approver->name}",
            'info',
            $approver->id
        );

        return $otpRequest;
    }

    public function denyOtp(OtpRequest $otpRequest, User $approver, string $notes = null)
    {
        if ($otpRequest->status !== 'pending') {
            throw new \Exception('OTP request is no longer pending.');
        }

        $otpRequest->deny($approver, $notes);

        // Log the denial
        SystemLog::logAction(
            'otp_denied',
            'otp',
            "OTP denied for user {$otpRequest->user->name} by {$approver->name}",
            'info',
            $approver->id
        );

        return $otpRequest;
    }

    public function verifyOtp(User $user, string $otpCode)
    {
        $otpRequest = $user->otpRequests()
            ->where('otp_code', $otpCode)
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if (!$otpRequest) {
            throw new \Exception('Invalid or expired OTP code.');
        }

        // Mark OTP as used
        $otpRequest->markAsUsed();

        // Create OTP session for the user (valid for 2 hours)
        $user->update([
            'otp_session_expires_at' => now()->addHours(2)
        ]);

        // Log the verification
        SystemLog::logAction(
            'otp_verified',
            'otp',
            'OTP successfully verified',
            'info',
            $user->id
        );

        return $otpRequest;
    }

    public function expireOldOtpRequests()
    {
        $expiredRequests = OtpRequest::where('expires_at', '<', now())
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        foreach ($expiredRequests as $request) {
            $request->markAsExpired();
        }

        return $expiredRequests->count();
    }

    public function getPendingOtpRequests()
    {
        return OtpRequest::with(['user'])
            ->pending()
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getValidOtpForUser(User $user)
    {
        return $user->otpRequests()
            ->where('status', 'used')
            ->where('expires_at', '>', now()->subHours(2))
            ->whereNotNull('used_at')
            ->orderBy('used_at', 'desc')
            ->first();
    }

    // New enhanced methods for complete workflow
    public function revokeUserOtpSession(User $user)
    {
        $user->revokeOtpSession();
        
        SystemLog::logAction(
            'otp_session_revoked',
            'otp',
            'OTP session revoked',
            'info',
            $user->id
        );
    }

    public function extendUserOtpSession(User $user, int $hours = 2)
    {
        $user->extendOtpSession($hours);
        
        SystemLog::logAction(
            'otp_session_extended',
            'otp',
            "OTP session extended by {$hours} hours",
            'info',
            $user->id
        );
    }

    protected function notifyAdminsOfNewRequest(OtpRequest $otpRequest)
    {
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        
        foreach ($admins as $admin) {
            try {
                // You can implement admin notification email here
                // Mail::to($admin->email)->send(new NewOtpRequestNotification($otpRequest));
                
                // For now, just log the notification
                SystemLog::logAction(
                    'admin_notified',
                    'otp',
                    "Admin {$admin->name} notified of new OTP request from {$otpRequest->user->name}",
                    'info'
                );
            } catch (\Exception $e) {
                SystemLog::logAction(
                    'admin_notification_failed',
                    'otp',
                    "Failed to notify admin {$admin->email} of new OTP request: {$e->getMessage()}",
                    'warning'
                );
            }
        }
    }

    public function getOtpStatistics()
    {
        return [
            'pending' => OtpRequest::pending()->where('expires_at', '>', now())->count(),
            'approved_today' => OtpRequest::where('status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'denied_today' => OtpRequest::where('status', 'denied')
                ->whereDate('approved_at', today())->count(),
            'used_today' => OtpRequest::where('status', 'used')
                ->whereDate('used_at', today())->count(),
            'expired' => OtpRequest::where('status', 'expired')->count(),
        ];
    }

    public function cleanupExpiredSessions()
    {
        $expiredUsers = User::where('otp_session_expires_at', '<', now())
            ->whereNotNull('otp_session_expires_at')
            ->get();

        foreach ($expiredUsers as $user) {
            $user->revokeOtpSession();
        }

        return $expiredUsers->count();
    }

    // Bulk operations for admin efficiency
    public function bulkApproveRequests(array $requestIds, User $approver, string $notes = null)
    {
        $approved = 0;
        $errors = [];

        foreach ($requestIds as $id) {
            try {
                $otpRequest = OtpRequest::findOrFail($id);
                $this->approveOtp($otpRequest, $approver, $notes);
                $approved++;
            } catch (\Exception $e) {
                $errors[] = "Request ID {$id}: " . $e->getMessage();
            }
        }

        return [
            'approved' => $approved,
            'errors' => $errors,
            'total' => count($requestIds)
        ];
    }

    public function bulkDenyRequests(array $requestIds, User $approver, string $notes = null)
    {
        $denied = 0;
        $errors = [];

        foreach ($requestIds as $id) {
            try {
                $otpRequest = OtpRequest::findOrFail($id);
                $this->denyOtp($otpRequest, $approver, $notes);
                $denied++;
            } catch (\Exception $e) {
                $errors[] = "Request ID {$id}: " . $e->getMessage();
            }
        }

        return [
            'denied' => $denied,
            'errors' => $errors,
            'total' => count($requestIds)
        ];
    }

    // Check user's current OTP status
    public function getUserOtpStatus(User $user)
    {
        return $user->getOtpStatus();
    }

    // Force cleanup for specific user
    public function cleanupUserOtpData(User $user)
    {
        // Expire any pending requests
        $user->otpRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->update(['status' => 'expired']);

        // Revoke OTP session
        $user->revokeOtpSession();

        SystemLog::logAction(
            'otp_data_cleanup',
            'otp',
            'All OTP data cleaned up for user',
            'info',
            $user->id
        );
    }
}