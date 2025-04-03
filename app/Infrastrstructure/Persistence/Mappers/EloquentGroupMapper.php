<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence\Mappers;

use App\Domain\Entity\Debt;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Group;
use App\Domain\Entity\Payer;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseDebt;
use App\Models\ExpensePay;

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
            name         : $group->name,
            category     : $group->category,
            createdBy    : $group->created_by,
            finalCurrency: $group->final_currency,
            simplifyDebts: $group->simplify_debts,
            id           : $group->id,
            members      : $group->members->map(function (Customer $customer) {
                return new \App\Domain\Entity\Customer(
                    password: $customer->password,
                    email   : $customer->email,
                    id      : $customer->id,
                );
            })->toArray(),
            expenses     : $group->expenses->map(function (Expense $expense) {
                return new \App\Domain\Entity\Expense(
                    category   : $expense->category,
                    createdAt  : $expense->created_at->timestamp,
                    currency   : $expense->final_currency,
                    description: $expense->description,
                    id         : $expense->id,
                    debts      : $expense->debts->map(function (ExpenseDebt $expenseDebt) {
                        return new Debt(
                            amount  : (float)$expenseDebt->amount,
                            currency: $expenseDebt->currency,
                            from    : $expenseDebt->from,
                            to      : $expenseDebt->to,
                            status  : $expenseDebt->status,
                            id      : $expenseDebt->id,
                        );
                    })->toArray(),
                    debtors    : $expense->debts->map(function (ExpenseDebt $expenseDebt) {
                        return new Debtor(
                            id    : $expenseDebt->from,
                            amount: (float)$expenseDebt->amount,
                        );
                    })->toArray(),
                    payers     : $expense->pays->map(function (ExpensePay $expensePay) {
                        return new Payer(
                            id      : $expensePay->payer_id,
                            amount  : (float)$expensePay->amount,
                            currency: $expensePay->currency,
                            avatar  : $expensePay->customer->avatar
                        );
                    })->toArray(),
                );
            })->toArray(),
        );
    }
}
