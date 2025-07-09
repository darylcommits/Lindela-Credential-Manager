<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Credential;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Storage;

class ProcessCredentialBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $encrypt;

    public function __construct($encrypt = true)
    {
        $this->encrypt = $encrypt;
    }

    public function handle()
    {
        $credentials = Credential::with(['category', 'creator'])->get();
        
        $backupData = [
            'created_at' => now()->toISOString(),
            'total_credentials' => $credentials->count(),
            'encrypted' => $this->encrypt,
            'credentials' => $credentials->map(function ($credential) {
                return [
                    'id' => $credential->id,
                    'category' => $credential->category->name,
                    'name' => $credential->name,
                    'username' => $credential->username,
                    'email' => $credential->email,
                    'password' => $credential->password, // Already encrypted
                    'website_url' => $credential->website_url,
                    'notes' => $credential->notes,
                    'backup_codes' => $credential->backup_codes,
                    'expires_at' => $credential->expires_at,
                    'created_by' => $credential->creator->name,
                    'created_at' => $credential->created_at->toISOString(),
                ];
            })
        ];
        
        $filename = 'credentials-backup-' . now()->format('Y-m-d-H-i-s') . '.json';
        $content = json_encode($backupData, JSON_PRETTY_PRINT);
        
        if ($this->encrypt) {
            $content = encrypt($content);
            $filename = str_replace('.json', '.encrypted', $filename);
        }
        
        Storage::disk('local')->put("backups/{$filename}", $content);
        
        SystemLog::logAction(
            'backup_created',
            'system',
            "Credentials backup created: {$filename}",
            'info'
        );
    }

    public function failed(\Throwable $exception)
    {
        SystemLog::logAction(
            'backup_failed',
            'system',
            "Backup failed: {$exception->getMessage()}",
            'error'
        );
    }
}