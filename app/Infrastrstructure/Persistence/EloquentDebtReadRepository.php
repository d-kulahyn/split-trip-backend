<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Debt;
use App\Infrastrstructure\Persistence\Mappers\EloquentCustomerMapper;
use App\Models\ExpenseDebt;
use App\Domain\Repository\DebtReadRepositoryInterface;

class EloquentDebtReadRepository implements DebtReadRepositoryInterface
{

    public function findById(int $id): ?Debt
    {
        $debt = ExpenseDebt::query()->find($id);

        if ($debt === null) {
            return null;
        }

        return new Debt(
            amount  : (float)$debt->amount,
            currency: $debt->currency,
            groupId : $debt->group_id,
            from    : EloquentCustomerMapper::map($debt->debtor),
            to      : EloquentCustomerMapper::map($debt->creditor),
            status  : $debt->status,
            id      : $debt->id,
        );
    }
}
