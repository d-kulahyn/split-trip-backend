<?php

namespace App\Infrastrstructure\API\Enum;

enum ConfirmationEmailCodeRuleEnum: int
{
    case IS_EXPIRED = 1;
    case IS_WRONG = 2;
}
