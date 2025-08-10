<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Customer;
use App\Domain\ValueObject\Balance;

interface   BalanceWriteRepositoryInterface
{
    /**
     * @param array<Balance> $balances
     * @param Customer $customer
     *
     * @return void
     */
    public function update(array $balances, Customer $customer): void;
}
