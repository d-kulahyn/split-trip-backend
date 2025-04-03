<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification;

use Illuminate\Support\Facades\Mail;
use App\Domain\Notification\NotificationChannelInterface;
use App\Infrastrstructure\Notification\Messages\EmailMessageInterface;

class EmailNotification implements NotificationChannelInterface
{
    public function send(object $message): void
    {
        if (!is_subclass_of($message, EmailMessageInterface::class)) return;

        Mail::to(request()->user()->email)->sendNow($message->email());
    }
}
