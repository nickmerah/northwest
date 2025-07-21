<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetLink;


    public function __construct($resetLink)
    {
        $this->resetLink = $resetLink;
    }


    public function build()
    {
        return $this->view('emails.password_reset')
            ->subject('Password Reset Request')
            ->with(['resetLink' => $this->resetLink]);
    }
}
