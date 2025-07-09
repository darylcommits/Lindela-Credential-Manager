<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // REMOVED LogsActivity temporarily
    
    // TEMPORARILY DISABLED: use LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_recovery_codes',
        'is_active',
        'last_activity_at',
        'otp_session_expires_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'otp_session_expires_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'is_active' => 'boolean',
    ];

    // EMERGENCY: Override delete method to prevent auto-deletion
    public function delete()
    {
        // Log ALL deletion attempts with full stack trace
        Log::emergency('🚨 USER DELETION ATTEMPT INTERCEPTED', [
            'user_id' => $this->id,
            'user_name' => $this->name,
            'user_email' => $this->email,
            'user_role' => $this->role,
            'is_active' => $this->is_active,
            'timestamp' => now(),
            'stack_trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50)
        ]);

        // Check if this is an intentional deletion from admin controller
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
        $isIntentionalDeletion = false;
        
        foreach ($trace as $frame) {
            if (isset($frame['class']) && 
                $frame['class'] === 'App\Http\Controllers\Admin\UserManagementController' &&
                isset($frame['function']) && $frame['function'] === 'destroy') {
                $isIntentionalDeletion = true;
                break;
            }
        }
        
        if (!$isIntentionalDeletion) {
            Log::emergency('🛡️ BLOCKED AUTOMATIC USER DELETION', [
                'user_id' => $this->id,
                'user_name' => $this->name,
                'blocked_at' => now(),
                'reason' => 'Not from admin controller'
            ]);
            
            // BLOCK the deletion
            return false;
        }

        // If it's intentional, allow it
        Log::info('✅ ALLOWING INTENTIONAL USER DELETION', [
            'user_id' => $this->id,
            'user_name' => $this->name,
            'deleted_by_admin' => true
        ]);

        return parent::delete();
    }

    // Also override forceDelete to be safe
    public function forceDelete()
    {
        Log::emergency('🚨 FORCE DELETE ATTEMPT BLOCKED', [
            'user_id' => $this->id,
            'user_name' => $this->name,
            'stack_trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20)
        ]);

        // Block ALL force deletions for now
        return false;
    }

    // TEMPORARILY DISABLED ActivityLog to see if it's causing issues
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontLogAny();  // Disable all logging temporarily
    }

    // Relationships
    public function createdCredentials()
    {
        return $this->hasMany(Credential::class, 'created_by');
    }

    public function updatedCredentials()
    {
        return $this->hasMany(Credential::class, 'updated_by');
    }

    public function otpRequests()
    {
        return $this->hasMany(OtpRequest::class);
    }

    public function approvedOtpRequests()
    {
        return $this->hasMany(OtpRequest::class, 'approved_by');
    }

    public function credentialAccessLogs()
    {
        return $this->hasMany(CredentialAccessLog::class);
    }

    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class);
    }

    // Basic helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isItStaff()
    {
        return $this->role === 'it_staff';
    }

    public function canAccessCredentials()
    {
        return $this->isAdmin() || $this->isItStaff();
    }

    public function canLogin()
    {
        return $this->is_active && $this->email_verified_at !== null;
    }

    // Email verification methods
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    // Activity tracking methods
    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    // Enhanced OTP-related methods
    public function hasValidOtp()
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->otp_session_expires_at && $this->otp_session_expires_at->isFuture()) {
            return true;
        }

        $validOtp = $this->otpRequests()
            ->where('status', 'used')
            ->where('expires_at', '>', now())
            ->whereNotNull('used_at')
            ->where('used_at', '>', now()->subHours(2))
            ->first();

        if ($validOtp) {
            $this->update([
                'otp_session_expires_at' => now()->addHours(2)
            ]);
            return true;
        }

        return false;
    }

    public function getCurrentOtpRequest()
    {
        return $this->otpRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function getApprovedOtpRequest()
    {
        return $this->otpRequests()
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();
    }

    public function getPendingOtpRequest()
    {
        return $this->otpRequests()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function extendOtpSession($hours = 2)
    {
        $this->update([
            'otp_session_expires_at' => now()->addHours($hours)
        ]);
    }

    public function revokeOtpSession()
    {
        $this->update([
            'otp_session_expires_at' => null
        ]);
    }

    public function getOtpStatus()
    {
        if ($this->isAdmin()) {
            return [
                'status' => 'admin_access',
                'message' => 'Admin users have full access',
                'action' => 'access_credentials'
            ];
        }

        if ($this->hasValidOtp()) {
            return [
                'status' => 'valid_access',
                'message' => 'You have valid OTP access',
                'action' => 'access_credentials',
                'expires_at' => $this->otp_session_expires_at
            ];
        }

        $approvedOtp = $this->getApprovedOtpRequest();
        if ($approvedOtp) {
            return [
                'status' => 'needs_verification',
                'message' => 'Please verify your OTP code',
                'action' => 'verify_otp',
                'expires_at' => $approvedOtp->expires_at,
                'time_remaining' => $approvedOtp->time_remaining ?? null
            ];
        }

        $pendingOtp = $this->getPendingOtpRequest();
        if ($pendingOtp) {
            return [
                'status' => 'pending_approval',
                'message' => 'Your OTP request is pending admin approval',
                'action' => 'wait_for_approval',
                'expires_at' => $pendingOtp->expires_at,
                'time_remaining' => $pendingOtp->time_remaining ?? null
            ];
        }

        return [
            'status' => 'no_access',
            'message' => 'You need to request OTP access',
            'action' => 'request_otp'
        ];
    }

    // Enhanced role checking methods
    public function canManageUsers()
    {
        return $this->isAdmin();
    }

    public function canManageOtp()
    {
        return $this->isAdmin();
    }

    public function canAccessCredentialsWithOtp()
    {
        return $this->isAdmin() || $this->hasValidOtp();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeItStaff($query)
    {
        return $query->where('role', 'it_staff');
    }

    public function scopeWithValidOtp($query)
    {
        return $query->where(function ($q) {
            $q->where('role', 'admin')
              ->orWhere('otp_session_expires_at', '>', now());
        });
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Two-Factor Authentication methods
    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled;
    }

    public function getTwoFactorRecoveryCodes()
    {
        return $this->two_factor_recovery_codes ?? [];
    }

    public function enableTwoFactor($secret, $recoveryCodes = [])
    {
        $this->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);
    }

    public function disableTwoFactor()
    {
        $this->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
        ]);
    }

    // Helper methods for debugging
    public function getDebugInfo()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at?->toDateTimeString(),
            'can_login' => $this->canLogin(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}