<?php

namespace App\Infrastrstructure\API\Enum;

use App\Shared\Trait\CacheKeysTrait;

enum GroupCacheEnum: string
{
    use CacheKeysTrait;

    case USER_GROUP_BALANCE = 'users:{user_id}:group_balance';
    case USER_OVERALL_BALANCE = 'users:{user_id}:overall_balance';
}
