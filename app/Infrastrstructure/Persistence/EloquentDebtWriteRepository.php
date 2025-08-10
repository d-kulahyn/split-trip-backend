<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Debt;
use App\Models\ExpenseDebt;
use App\Domain\Repository\DebtWriteRepositoryInterface;

class EloquentDebtWriteRepository implements DebtWriteRepositoryInterface
{
    /**
     * @param Debt $debt
     *
     * @return void
     */
    public function save(Debt $debt): void
    {
        ExpenseDebt::query()->updateOrCreate(
            ['id' => $debt->id],
            [
                'amount'   => $debt->amount,
                'currency' => $debt->currency,
                'from'     => $debt->from->id,
                'to'       => $debt->to->id,
                'status'   => $debt->status->value,
            ]
        );
    }
}
