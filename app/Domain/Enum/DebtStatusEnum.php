<?php

namespace App\Domain\Enum;

use App\Shared\Trait\AllCasesTrait;

enum DebtStatusEnum: string
{
    use AllCasesTrait;

    case PENDING = 'pending';
    case PAID = 'paid';
}
