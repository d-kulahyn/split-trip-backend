<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence\Mappers;

use App\Domain\Entity\Debt;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Group;
use App\Domain\Entity\Payer;
use App\Domain\Repository\DebtWriteRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use App\Infrastrstructure\Mapper\CustomerEloquentToDomainEntity;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseDebt;

class EloquentGroupMapper
{
    /**
     * @param \App\Models\Group $group
     *
     * @return Group
     */
    public static function map(\App\Models\Group $group): Group
    {
        return new Group(
            name                      : $group->name,
            category                  : $group->category,
            createdBy                 : $group->created_by,
            finalCurrency             : $group->final_currency,
            debtWriteRepository       : app(DebtWriteRepositoryInterface::class),
            transactionWriteRepository: app(TransactionWriteRepositoryInterface::class),
            simplifyDebts             : $group->simplify_debts,
            id                        : $group->id,
            debts                     : $group->debts->map(function (ExpenseDebt $debt) {
                return new Debt(
                    amount  : (float)$debt->amount,
                    currency: $debt->currency,
                    groupId : $debt->group_id,
                    from    : EloquentCustomerMapper::map($debt->debtor),
                    to      : EloquentCustomerMapper::map($debt->creditor),
                    status  : $debt->status,
                    id      : $debt->id,
                );
            })->all(),
            members                   : $group->members->keyBy('id')->map(function (Customer $customer) {
                return CustomerEloquentToDomainEntity::toEntity($customer);
            })->all(),
            expenses                  : $group->expenses->map(function (Expense $expense) {
                return new \App\Domain\Entity\Expense(
                    category   : $expense->category,
                    createdAt  : $expense->created_at->timestamp,
                    currency   : $expense->final_currency,
                    description: $expense->description,
                    groupId    : $expense->group_id,
                    id         : $expense->id,
                    debts      : $expense->debts->map(function (ExpenseDebt $expenseDebt) {
                        return new Debt(
                            amount  : (float)$expenseDebt->amount,
                            currency: $expenseDebt->currency,
                            groupId : $expenseDebt->group_id,
                            from    : EloquentCustomerMapper::map($expenseDebt->debtor),
                            to      : EloquentCustomerMapper::map($expenseDebt->creditor),
                            status  : $expenseDebt->status,
                            id      : $expenseDebt->id,
                        );
                    })->all(),
                    debtors    : $expense->debtors->map(function (\App\Models\Debtor $debtor) {
                        return new Debtor(
                            amount     : $debtor->amount,
                            debtorId   : $debtor->debtor_id,
                            currency   : $debtor->currency,
                            id         : $debtor->id,
                            name       : $debtor->customer->name,
                            avatarColor: $debtor->customer->avatar_color,
                        );
                    })->all(),
                    payers     : $expense->payers->map(function (\App\Models\Payer $payer) {
                        return new Payer(
                            amount     : $payer->amount,
                            currency   : $payer->currency,
                            payerId    : $payer->payer_id,
                            id         : $payer->id,
                            avatarColor: $payer->customer->avatar_color,
                            name       : $payer->customer->name
                        );
                    })->all(),
                );
            })->all(),
        );
    }
}
