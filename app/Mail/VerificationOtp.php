<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $code; 

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $code = $this->code;
        return $this->subject("Code OTP verification BCB mobile")->from('bmo-uba-noreply@bestcash.me')->view('mail.verificationOtp',compact('code'));
    }
}
