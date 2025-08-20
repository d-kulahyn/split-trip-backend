<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification\Messages;

use ArrayObject;
use Illuminate\Contracts\Mail\Mailable;
use App\Application\Mail\RemindDebtEmail;
use Kreait\Firebase\Messaging\CloudMessage;

class RemindDebtMessage extends ArrayObject
    implements FirebaseCloudMessagingInterface,
               EmailMessageInterface
{
    /**
     * @return Mailable
     */
    public function email(): Mailable
    {
        return new RemindDebtEmail(
            $this->offsetGet('amount'),
            $this->offsetGet('currency'),
            $this->offsetGet('groupName'),
            $this->offsetGet('creditorName')
        );
    }

    /**
     * @return CloudMessage
     */
    public function fcm(): CloudMessage
    {
        return CloudMessage::new()
            ->withNotification([
                'title' => 'Хуй Reminder',
                'body'  => "Ты хуй 😂",
            ])
            ->withData([
                'test' => 'data',
            ])
            ->toToken($this->offsetGet('token'));
    }
}
