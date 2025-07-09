<?php

namespace App\Mail;

use App\Models\OtpRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpRequest;

    public function __construct(OtpRequest $otpRequest)
    {
        $this->otpRequest = $otpRequest;
    }

    public function build()
    {
        return $this->view('emails.otp-approved')
                    ->subject('Your OTP Request Has Been Approved')
                    ->with([
                        'otpCode' => $this->otpRequest->otp_code,
                        'purpose' => $this->otpRequest->purpose,
                        'expiresAt' => $this->otpRequest->expires_at,
                    ]);
    }
}