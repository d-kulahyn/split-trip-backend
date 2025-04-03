<?php

namespace App\Domain\Enum;

use App\Shared\Trait\AllCasesTrait;

enum DebtReminderPeriodEnum: string
{
    use AllCasesTrait;

    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
}
