<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Debt;
use App\Models\ExpenseDebt;
use App\Domain\Repository\DebtReadRepositoryInterface;

class EloquentDebtReadRepository implements DebtReadRepositoryInterface
{

    public function findById(int $id): ?Debt
    {
        $debt = Debt::query()->find($id);

        if ($debt === null) {
            return null;
        }

        return new Debt(
            amount: (float)$debt->amount,
            currency: $debt->currency,
            from: $debt->from,
            to: $debt->to,
            status: $debt->status,
            groupId: $debt->group_id,
            id: $debt->id,
        );
    }
}
