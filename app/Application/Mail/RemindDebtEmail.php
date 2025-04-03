<?php

namespace App\Application\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class RemindDebtEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected float $debtAmount,
        protected string $debtCurrency,
        protected string $groupName,
        protected string $debtorName
    ) {}

    /**
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.remind',
            with    : [
                'debtAmount'   => $this->debtAmount,
                'debtCurrency' => $this->debtCurrency,
                'groupName'    => $this->groupName,
                'debtorName'   => $this->debtorName,
            ]
        );
    }
}
