<?php

namespace App\Application\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected string $newPassword
    ) {}

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.password.reset',
            with    : [
                'password'   => $this->newPassword,
            ]
        );
    }
}
