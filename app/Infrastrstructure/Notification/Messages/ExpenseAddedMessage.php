<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification\Messages;

use ArrayObject;
use Kreait\Firebase\Messaging\CloudMessage;

/**
 * Class ExpenseAddedMessage
 *
 * Represents a message for a newly added expense, which can be sent via FCM.
 *
 * @property-read non-empty-string $customer_name
 * @property-read non-empty-string $group_name
 * @property-read non-empty-string $token
 * @property-read string $date
 * @property-read float $amount
 *
 * @extends ArrayObject<string, mixed>
 * @implements FirebaseCloudMessagingInterface
 */
class ExpenseAddedMessage extends ArrayObject implements FirebaseCloudMessagingInterface
{
    /**
     * @return CloudMessage
     */
    public function fcm(): CloudMessage
    {
        return CloudMessage::new()
            ->withNotification([
                'title' => '🆕 New Expense',
                'body'  => "👤 {$this->customer_name} created expense in group {$this->group_name} with amount 💵 {$this->amount}",
            ])
            ->toToken($this->token);
    }

    public function __get(string $key)
    {
        if (!$this->offsetExists($key)) {
            throw new \InvalidArgumentException("Property {$key} does not exist in " . self::class);
        }

        return $this->offsetGet($key);
    }
}
