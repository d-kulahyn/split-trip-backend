<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification\Messages;

use Kreait\Firebase\Messaging\CloudMessage;

/**
 * Class ExpenseAddedMessage
 *
 * @property-read non-empty-string $customer_name
 * @property-read non-empty-string $group_name
 * @property-read non-empty-string $token
 * @property-read string $date
 * @property-read float $amount
 *
 */
class ExpenseAddedMessage extends BaseMessage
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
}
