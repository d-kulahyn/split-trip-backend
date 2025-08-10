<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Transaction;

interface TransactionWriteRepositoryInterface
{
    public function save(Transaction $transaction): Transaction;
}
