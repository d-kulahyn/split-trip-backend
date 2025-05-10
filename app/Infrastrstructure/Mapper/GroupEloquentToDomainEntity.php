<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Mapper;

use App\Domain\Entity\Debt;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Expense;
use App\Domain\Entity\Payer;
use App\Models\Group;
use App\Models\ExpenseDebt;
use App\Models\Payer as PayerModel;
use App\Models\Debtor as DebtorModel;

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
            debts      : $groupEloquent->debts->map(function (ExpenseDebt $debt) {
                return new Debt(
                    amount  : (float)$debt->amount,
                    currency: $debt->currency,
                    groupId : $debt->group_id,
                    from    : $debt->from,
                    to      : $debt->to,
                    status  : $debt->status,
                    id      : $debt->id
                );
            })->toArray(),
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
                    groupId    : $expense->group_id,
                    id         : $expense->id,
                    debts      : $expense->debts->map(function (ExpenseDebt $debt) {
                        return new Debt(
                            amount  : (float)$debt->amount,
                            currency: $debt->currency,
                            groupId : $debt->group_id,
                            from    : $debt->from,
                            to      : $debt->to,
                            status  : $debt->status,
                            id      : $debt->id
                        );
                    })->toArray(),
                    debtors    : $expense->debtors->map(function (DebtorModel $debtor) {
                        return new Debtor(
                            amount  : $debtor->amount,
                            debtorId: $debtor->debtor_id,
                            currency: $debtor->currency,
                            id      : $debtor->id,
                            name    : $debtor->customer->name,
                            avatar  : $debtor->customer->avatar,
                        );
                    })->toArray(),
                    payers     : $expense->payers->map(function (PayerModel $payer) {
                        return new Payer(
                            amount  : $payer->amount,
                            currency: $payer->currency,
                            payerId : $payer->payer_id,
                            id      : $payer->id,
                            avatarColor  : $payer->customer->avatar_color,
                            name    : $payer->customer->name,
                        );
                    })->toArray(),
                ));
            }
        }

        return $domainGroup;
    }
}
