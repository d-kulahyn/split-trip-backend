<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification\Messages;

use Illuminate\Contracts\Mail\Mailable;
use App\Application\Mail\RemindDebtEmail;
use Kreait\Firebase\Messaging\CloudMessage;

/**
 * Class RemindDebtMessage
 *
 * @@property-read float $amount
 * @@property-read string $currency
 * @@property-read string $groupName
 * @@property-read string $creditorName
 * @@property-read string|null $token
 */
class RemindDebtMessage extends BaseMessage implements FirebaseCloudMessagingInterface, EmailMessageInterface
{
    /**
     * @return Mailable
     */
    public function email(): Mailable
    {
        return new RemindDebtEmail(
            $this->amount,
            $this->currency,
            $this->groupName,
            $this->creditorName
        );
    }

    /**
     * @return CloudMessage
     */
    public function fcm(): CloudMessage
    {
        return CloudMessage::new()
            ->withNotification([
                'title' => '👺 Debt Reminder',
                'body'  => "You owe 💵 {$this->amount} {$this->currency} to 👤 {$this->creditorName} in {$this->groupName}",
            ])
            ->toToken($this->token);
    }
}
