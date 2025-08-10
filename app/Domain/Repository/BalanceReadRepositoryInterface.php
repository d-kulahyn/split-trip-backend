<?php

namespace App\Domain\Repository;


use App\Domain\ValueObject\Balance;

interface BalanceReadRepositoryInterface
{
    /**
     * @param array $customerIds
     *
     * @return array<Balance>
     */
    public function getGroupBalances(array $customerIds): array;
    public function getOverallBalance(int $customerId): ?Balance;
}
