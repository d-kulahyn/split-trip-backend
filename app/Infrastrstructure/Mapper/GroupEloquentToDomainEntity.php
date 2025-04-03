<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Mapper;

use App\Domain\Entity\Debt;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Expense;
use App\Domain\Entity\Pay;
use App\Domain\Entity\Payer;
use App\Models\ExpenseDebt;
use App\Models\ExpensePay;
use App\Models\Group;

class GroupEloquentToDomainEntity
{
    /**
     * @param Group $groupEloquent
     *
     * @return \App\Domain\Entity\Group
     */
    public static function toEntity(Group $groupEloquent): \App\Domain\Entity\Group
    {
        $domainGroup = new \App\Domain\Entity\Group(
            name         : $groupEloquent->name,
            category     : $groupEloquent->category,
            createdBy    : $groupEloquent->created_by,
            finalCurrency: $groupEloquent->final_currency,
            simplifyDebts: $groupEloquent->simplify_debts,
            id           : $groupEloquent->id,
            avatar       : $groupEloquent->avatar,
        );

        if ($groupEloquent->relationLoaded('members')) {
            foreach ($groupEloquent->members as $member) {
                $domainGroup->addMember(CustomerEloquentToDomainEntity::toEntity($member));
            }

            $domainGroup->addMember(CustomerEloquentToDomainEntity::toEntity($groupEloquent->owner));
        }

        if ($groupEloquent->relationLoaded('expenses')) {
            foreach ($groupEloquent->expenses as $expense) {
                $domainGroup->addExpense(new Expense(
                    category   : $expense->category,
                    createdAt  : $expense->created_at->timestamp,
                    currency   : $expense->final_currency,
                    description: $expense->description,
                    id         : $expense->id,
                    debts      : $expense->debts->map(function (ExpenseDebt $debt) {
                        return new Debt(
                            amount  : (float)$debt->amount,
                            currency: $debt->currency,
                            from    : $debt->from,
                            to      : $debt->to,
                            status  : $debt->status,
                            id      : $debt->id
                        );
                    })->toArray(),
                    pays       : $expense->pays->map(function (ExpensePay $pay) {
                        return new Pay(
                            amount  : (float)$pay->amount,
                            currency: $pay->currency,
                            payerId : $pay->payer_id,
                            id      : $pay->id
                        );
                    })->toArray(),
                    debtors    : $expense->debts->map(function (ExpenseDebt $debt) {
                        return new Debtor(
                            id    : $debt->from,
                            amount: (float)$debt->amount,
                        );
                    })->toArray(),
                    payers     : $expense->pays->map(function (ExpensePay $pay) {
                        return new Payer(
                            id      : $pay->payer_id,
                            amount  : (float)$pay->amount,
                            currency: $pay->currency,
                            avatar  : $pay->customer->avatar
                        );
                    })->toArray(),
                ));
            }
        }

        return $domainGroup;
    }
}
