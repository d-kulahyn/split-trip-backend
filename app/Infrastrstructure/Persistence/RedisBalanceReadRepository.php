<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Repository\BalanceReadRepositoryInterface;
use App\Domain\ValueObject\Balance;
use App\Infrastrstructure\API\Enum\GroupCacheEnum;
use Illuminate\Support\Facades\Cache;

class RedisBalanceReadRepository implements BalanceReadRepositoryInterface
{

    /**
     * @param array $customerIds
     *
     * @return array<Balance>
     */
    public function getGroupBalances(array $customerIds): array
    {
        $balances = [];

        foreach ($customerIds as $customerId) {
            $balances[$customerId] = Balance::from([
                ...(json_decode(Cache::get(
                    GroupCacheEnum::USER_GROUP_BALANCE->map(['{user_id}' => $customerId])) ?? '',
                    true
                ) ?? []),
                'customerId' => $customerId,
            ]);
        }

        return $balances;
    }

    /**
     * @param int $customerId
     *
     * @return Balance
     */
    public function getOverallBalance(int $customerId): Balance
    {
        $cachedBalance = Cache::get(GroupCacheEnum::USER_OVERALL_BALANCE->map(['{user_id}' => $customerId]));

        if ($cachedBalance) {
            return Balance::from([...json_decode($cachedBalance, true), 'customerId' => $customerId]);
        }

        return new Balance();
    }
}
