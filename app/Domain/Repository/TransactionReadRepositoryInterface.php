<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Transaction;
use App\Domain\Enum\StatusEnum;
use Illuminate\Support\Collection;

interface TransactionReadRepositoryInterface
{
    public function list(StatusEnum $status, int $to): Collection;
    public function getById(int $id): ?Transaction;
    public function getPendingAmount(int $to);
}
