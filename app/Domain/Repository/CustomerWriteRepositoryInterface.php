<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Customer;

interface CustomerWriteRepositoryInterface
{
    public function save(Customer $customer);
    public function createToken(int $customerId): string;
    public function removeTokens(int $customerId): void;
}
