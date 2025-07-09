<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\OtpRequest;
use App\Mail\OtpRequestMail;

class SendOtpNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $otpRequest;

    public function __construct(OtpRequest $otpRequest)
    {
        $this->otpRequest = $otpRequest;
    }

    public function handle()
    {
        Mail::to($this->otpRequest->user->email)->send(new OtpRequestMail($this->otpRequest));
    }

    public function failed(\Throwable $exception)
    {
        \App\Models\SystemLog::logAction(
            'otp_notification_failed',
            'otp',
            "Failed to send OTP notification: {$exception->getMessage()}",
            'error',
            $this->otpRequest->user_id
        );
    }
}