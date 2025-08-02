<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class CustomVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify', now()->addMinutes(60), ['id' => $this->user->getKey(), 'hash' => sha1($this->user->getEmailForVerification())]
        );

        return $this->subject('Verify Your Email Address')
                    ->view('emails.custom-verification')
                    ->with([
                        'verificationUrl' => $verificationUrl,
                        'userName' => $this->user->name,
                    ]);
    }
}
