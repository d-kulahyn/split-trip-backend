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
    public function save(Transaction $transaction): Transaction
    {
        $eloquentTransaction = \App\Models\Transaction::query()->updateOrCreate(
            ['id' => $transaction->id],
            [
                'amount'   => $transaction->amount,
                'currency' => $transaction->currency,
                'rate'     => $transaction->rate,
                'from'     => $transaction->from->id,
                'to'       => $transaction->to->id,
                'group_id' => $transaction->groupId,
                'status'   => $transaction->status,
            ]
        );

        $transaction->id = $eloquentTransaction->id;

        return $transaction;
    }
}
