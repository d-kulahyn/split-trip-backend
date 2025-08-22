<?php

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;
use App\Domain\Enum\DebtReminderPeriodEnum;

class UpdateCustomerDTO extends Data
{
    /**
     * @param string|null $name
     * @param string|null $currency
     * @param bool|null $email_notifications
     * @param bool|null $push_notifications
     * @param string|null $debt_reminder_period
     */
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $currency,
        public readonly ?bool $email_notifications,
        public readonly ?bool $push_notifications,
        public readonly ?string $debt_reminder_period
    ) {}


    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'name'                 => ['sometimes', 'min:3', 'alpha'],
            'email_notifications'  => ['sometimes', 'boolean'],
            'push_notifications'   => ['sometimes', 'boolean'],
            'currency'             => ['sometimes', 'string', 'size:3'],
            'debt_reminder_period' => ['sometimes', 'string', 'in:'.implode(',', DebtReminderPeriodEnum::values())],
        ];
    }
}
