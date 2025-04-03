<?php

namespace App\Domain\Enum;

use App\Shared\Trait\AllCasesTrait;

enum GroupCategoryEnum: string
{
    use AllCasesTrait;

    case TRIP = 'trip';
    case HOME = 'home';
    case COUPLE = 'couple';
    case OTHER = 'other';
}
