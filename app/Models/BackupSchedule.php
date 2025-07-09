<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BackupSchedule extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'frequency',
        'scheduled_time',
        'backup_types',
        'storage_path',
        'encrypt_backup',
        'retention_days',
        'last_run_at',
        'next_run_at',
        'is_active',
    ];

    protected $casts = [
        'backup_types' => 'array',
        'scheduled_time' => 'datetime:H:i:s',
        'encrypt_backup' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'frequency', 'scheduled_time', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('is_active', true)
                    ->where('next_run_at', '<=', now());
    }

    // Helper methods
    public function isDue()
    {
        return $this->is_active && $this->next_run_at->isPast();
    }

    public function updateNextRun()
    {
        $nextRun = match ($this->frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            default => now()->addDay(),
        };

        // Set the scheduled time
        $scheduledTime = $this->scheduled_time;
        $nextRun->setTime($scheduledTime->hour, $scheduledTime->minute, $scheduledTime->second);

        $this->update([
            'last_run_at' => now(),
            'next_run_at' => $nextRun,
        ]);
    }

    public function getFrequencyBadgeColorAttribute()
    {
        switch ($this->frequency) {
            case 'daily':
                return 'bg-green-100 text-green-800';
            case 'weekly':
                return 'bg-blue-100 text-blue-800';
            case 'monthly':
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getBackupTypesTextAttribute()
    {
        return collect($this->backup_types)->map(function ($type) {
            return ucfirst($type);
        })->join(', ');
    }
}
