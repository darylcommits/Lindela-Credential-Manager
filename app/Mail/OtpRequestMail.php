<?php

namespace App\Mail;

use App\Models\OtpRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpRequest;

    public function __construct(OtpRequest $otpRequest)
    {
        $this->otpRequest = $otpRequest;
    }

    public function build()
    {
        return $this->subject('OTP Request - Credentials Manager')
                    ->view('emails.otp-request');
    }
}