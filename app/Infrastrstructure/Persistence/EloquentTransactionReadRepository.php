<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Transaction;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Infrastrstructure\Mapper\CustomerEloquentToDomainEntity;
use App\Infrastrstructure\Mapper\GroupEloquentToDomainEntity;
use Illuminate\Support\Collection;

class EloquentTransactionReadRepository implements TransactionReadRepositoryInterface
{

    public function list(StatusEnum $status, int $to): Collection
    {
        return \App\Models\Transaction::query()
            ->where('status', $status->value)
            ->where('to', $to)
            ->with(['group' => fn($query) => $query->select(['id', 'name'])])
            ->get()
            ->map(function (\App\Models\Transaction $transaction) {
                return new Transaction(
                    from        : CustomerEloquentToDomainEntity::toEntity($transaction->fromC),
                    to          : CustomerEloquentToDomainEntity::toEntity($transaction->toC),
                    amount      : $transaction->amount,
                    currency    : $transaction->currency,
                    baseCurrency: $transaction->base_currency,
                    groupId     : $transaction->group_id,
                    groupName   : $transaction->group->name,
                    rate        : $transaction->rate,
                    id          : $transaction->id,
                    status      : $transaction->status
                );
            });
    }

    public function getById(int $id, array $with = []): ?Transaction
    {
        $transaction = \App\Models\Transaction::query()
            ->where('id', $id)
            ->with(['group'])
            ->first();

        if (!$transaction) {
            return null;
        }

        return new Transaction(
            from        : CustomerEloquentToDomainEntity::toEntity($transaction->fromC),
            to          : CustomerEloquentToDomainEntity::toEntity($transaction->toC),
            amount      : $transaction->amount,
            currency    : $transaction->currency,
            baseCurrency: $transaction->base_currency,
            groupId     : $transaction->group_id,
            groupName   : $transaction->group->name,
            rate        : $transaction->rate,
            id          : $transaction->id,
            group       : GroupEloquentToDomainEntity::toEntity($transaction->group),
            status      : $transaction->status
        );
    }

    public function getPendingAmount(int $to): float
    {
        return (float)\App\Models\Transaction::query()
            ->where('status', StatusEnum::PENDING)
            ->where('to', $to)
            ->sum('amount');
    }
}
