<?php

namespace App\Application\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected string $code
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    /**
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.verification',
            with    : [
                'code'   => $this->code,
            ]
        );
    }
}
