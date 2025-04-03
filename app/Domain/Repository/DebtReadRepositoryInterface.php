<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Debt;

interface DebtReadRepositoryInterface
{
    public function findById(int $id): ?Debt;
}
