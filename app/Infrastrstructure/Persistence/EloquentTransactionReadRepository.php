<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Transaction;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Infrastrstructure\Mapper\CustomerEloquentToDomainEntity;
use App\Infrastrstructure\Persistence\Mappers\EloquentGroupMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentTransactionReadRepository implements TransactionReadRepositoryInterface
{

    public function list(StatusEnum $status, int $to): Collection
    {
        return Cache::remember("transaction:{$to}", 3600, function () use ($status, $to) {
            return \App\Models\Transaction::query()
                ->where('status', $status->value)
                ->where('to', $to)
                ->with(['group' => fn($query) => $query->select(['id', 'name'])])
                ->get()
                ->map(function (\App\Models\Transaction $transaction) {
                    return new Transaction(
                        from     : CustomerEloquentToDomainEntity::toEntity($transaction->fromC),
                        to       : CustomerEloquentToDomainEntity::toEntity($transaction->toC),
                        amount   : $transaction->amount,
                        currency : $transaction->currency,
                        groupId  : $transaction->group_id,
                        groupName: $transaction->group->name,
                        id       : $transaction->id,
                        status   : $transaction->status,
                    );
                });
        });
    }

    public function getById(int $id, array $with = []): ?Transaction
    {
        $transaction = \App\Models\Transaction::query()
            ->where('id', $id)
            ->with(['group' => fn($query) => $query->select(['id', 'name'])])
            ->first();

        if (!$transaction) {
            return null;
        }

        return new Transaction(
            from     : CustomerEloquentToDomainEntity::toEntity($transaction->fromC),
            to       : CustomerEloquentToDomainEntity::toEntity($transaction->toC),
            amount   : $transaction->amount,
            currency : $transaction->currency,
            groupId  : $transaction->group_id,
            groupName: $transaction->group->name,
            id       : $transaction->id,
            status   : $transaction->status,
        );
    }
}
