<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Mapper;

use App\Models\ExpenseDebt;

class ExpenseDebtEloquentToDomainEntity
{

    public static function toEntity(ExpenseDebt $expenseDebt)
    {
        return new \App\Domain\Entity\Debt(
            amount  : (float)$expenseDebt->amount,
            currency: $expenseDebt->currency,
            from    : $expenseDebt->from,
            to      : $expenseDebt->to,
            status  : $expenseDebt->status,
            id      : $expenseDebt->id
        );
    }
}
