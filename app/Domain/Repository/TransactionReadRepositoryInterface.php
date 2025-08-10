<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Transaction;
use App\Domain\Enum\StatusEnum;
use Illuminate\Support\Collection;

interface TransactionReadRepositoryInterface
{
    public function list(StatusEnum $status, int $to, array $with = []): Collection;
    public function getById(int $id): ?Transaction;
}
