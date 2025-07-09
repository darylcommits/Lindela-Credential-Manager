<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Credential extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'category_id',
        'name',
        'username',
        'email',
        'password',
        'notes',
        'website_url',
        'backup_codes',
        'additional_fields',
        'expires_at',
        'password_never_expires',
        'password_strength',
        'last_accessed_at',
        'created_by',
        'updated_by',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'backup_codes' => 'array',
        'additional_fields' => 'array',
        'expires_at' => 'date',
        'last_accessed_at' => 'datetime',
        'password_never_expires' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'username', 'email', 'website_url', 'expires_at', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(CredentialCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(CredentialAccessLog::class);
    }

    // Accessors & Mutators
    public function getPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getEmailAttribute($value)
    {
        if (!$value) return null;
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setEmailAttribute($value)
    {
        if ($value) {
            $this->attributes['email'] = Crypt::encryptString($value);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
                    ->where('password_never_expires', false);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->where('password_never_expires', false);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Helper methods
    public function isExpired()
    {
        if ($this->password_never_expires) {
            return false;
        }

        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon($days = 30)
    {
        if ($this->password_never_expires) {
            return false;
        }

        return $this->expires_at && $this->expires_at->isBefore(now()->addDays($days));
    }

    public function getDaysUntilExpiration()
    {
        if ($this->password_never_expires || !$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    public function updateLastAccessed()
    {
        $this->update(['last_accessed_at' => now()]);
    }

    public function calculatePasswordStrength($password = null)
    {
        $password = $password ?: $this->password;
        
        $score = 0;
        
        // Length
        if (strlen($password) >= 8) $score++;
        if (strlen($password) >= 12) $score++;
        
        // Character types
        if (preg_match('/[a-z]/', $password)) $score++;
        if (preg_match('/[A-Z]/', $password)) $score++;
        if (preg_match('/[0-9]/', $password)) $score++;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score++;
        
        return min($score, 5);
    }

    public function getPasswordStrengthTextAttribute()
    {
        $strength = $this->password_strength;
        
        switch ($strength) {
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
                return 'Unknown';
        }
    }

    public function getPasswordStrengthColorAttribute()
    {
        $strength = $this->password_strength;
        
        switch ($strength) {
            case 1:
            case 2:
                return 'text-red-600';
            case 3:
                return 'text-yellow-600';
            case 4:
                return 'text-blue-600';
            case 5:
                return 'text-green-600';
            default:
                return 'text-gray-500';
        }
    }
}