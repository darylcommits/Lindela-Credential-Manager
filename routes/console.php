<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\OtpRequest;
use App\Services\OtpService;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire old OTP requests
Artisan::command('otp:expire', function () {
    $otpService = app(OtpService::class);
    $expired = $otpService->expireOldOtpRequests();
    $this->info("Expired {$expired} OTP requests.");
})->purpose('Expire old OTP requests');

// Clean old logs
Artisan::command('logs:clean', function () {
    $retentionDays = 180; // 6 months
    $cutoffDate = now()->subDays($retentionDays);
    
    $systemLogsDeleted = \App\Models\SystemLog::where('created_at', '<', $cutoffDate)->delete();
    $accessLogsDeleted = \App\Models\CredentialAccessLog::where('created_at', '<', $cutoffDate)->delete();
    
    $this->info("Deleted {$systemLogsDeleted} system logs and {$accessLogsDeleted} access logs older than {$retentionDays} days.");
})->purpose('Clean old system and access logs');
