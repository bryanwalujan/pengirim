<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->url = url(route('password.reset', [
            'token' => $token,
            'email' => $user->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->to($this->user->email) // Pastikan ada recipient
            ->subject('🔐 Reset Password E-Service UNIMA')
            ->view('emails.reset-password')
            ->with([
                'user' => $this->user,
                'url' => $this->url,
                'token' => $this->token,
            ]);
    }
}