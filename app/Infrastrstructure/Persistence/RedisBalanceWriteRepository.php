<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Customer;
use App\Domain\Repository\BalanceWriteRepositoryInterface;
use App\Domain\ValueObject\Balance;
use App\Infrastrstructure\API\Enum\GroupCacheEnum;
use Illuminate\Support\Facades\Cache;

class RedisBalanceWriteRepository implements BalanceWriteRepositoryInterface
{

    public function update(array $balances, Customer $customer): void
    {
        foreach($balances as $balance) {
            $cachedMemberBalance = Balance::from(json_decode(Cache::get(GroupCacheEnum::USER_GROUP_BALANCE->map(['{user_id}' => $balance->customerId])) ?? '', true) ?? []);

            $balance->balance = (float)bcadd((string)$cachedMemberBalance->balance, (string)$balance->balance);
            $balance->owe = (float)bcadd((string)$cachedMemberBalance->owe, (string)$balance->owe);
            $balance->paid = (float)bcadd((string)$cachedMemberBalance->paid, (string)$balance->paid);

            Cache::set(GroupCacheEnum::USER_GROUP_BALANCE->map(['{user_id}' => $balance->customerId]), $balance->toJson());
        }

        Cache::set(GroupCacheEnum::USER_OVERALL_BALANCE->map(['{user_id}' => $customer->id]), $customer->getBalance()->toJson());
    }
}
