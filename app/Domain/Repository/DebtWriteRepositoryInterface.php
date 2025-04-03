<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Debt;

interface DebtWriteRepositoryInterface
{
    public function save(Debt $debt);
}
