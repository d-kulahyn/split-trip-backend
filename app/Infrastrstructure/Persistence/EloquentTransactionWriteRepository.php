<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Transaction;
use App\Domain\Repository\TransactionWriteRepositoryInterface;

class EloquentTransactionWriteRepository implements TransactionWriteRepositoryInterface
{
    /**
     * @param Transaction $transaction
     *
     * @return void
     */
    public function save(Transaction $transaction): void
    {
        \App\Models\Transaction::query()->updateOrCreate(
            ['id' => $transaction->id],
            [
                'amount'   => $transaction->amount,
                'currency' => $transaction->currency,
                'from'     => $transaction->from,
                'to'       => $transaction->to,
                'group_id' => $transaction->groupId,
            ]
        );
    }
}
