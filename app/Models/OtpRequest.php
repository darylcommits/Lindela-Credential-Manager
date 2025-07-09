<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OtpRequest extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'otp_code',
        'purpose',
        'requested_resources',
        'status',
        'approved_by',
        'approved_at',
        'expires_at',
        'used_at',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'requested_resources' => 'array',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'purpose', 'approved_at', 'used_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function credentialAccessLogs()
    {
        return $this->hasMany(CredentialAccessLog::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->whereIn('status', ['pending', 'approved']);
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'approved')
                    ->where('expires_at', '>', now())
                    ->whereNull('used_at');
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isValid()
    {
        return $this->status === 'approved' && 
               !$this->isExpired() && 
               !$this->used_at;
    }

    public function isPending()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function canBeUsed()
    {
        return $this->isValid();
    }

    public function markAsUsed()
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function approve(User $approver, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function deny(User $approver, $notes = null)
    {
        $this->update([
            'status' => 'denied',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function markAsExpired()
    {
        if ($this->status === 'pending' || $this->status === 'approved') {
            $this->update(['status' => 'expired']);
        }
    }

    public static function generateOtp()
    {
        return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function getTimeRemainingAttribute()
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        $diff = now()->diffInMinutes($this->expires_at);
        
        if ($diff < 1) {
            return 'Less than 1 minute';
        }

        return $diff . ' minute' . ($diff > 1 ? 's' : '');
    }

    public function getStatusBadgeColorAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'approved':
                return 'bg-green-100 text-green-800';
            case 'denied':
                return 'bg-red-100 text-red-800';
            case 'expired':
                return 'bg-gray-100 text-gray-800';
            case 'used':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}

