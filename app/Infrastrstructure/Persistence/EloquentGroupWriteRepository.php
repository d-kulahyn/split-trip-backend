<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Customer;
use App\Domain\Entity\Expense;
use App\Events\GroupUpdatedEvent;
use App\Models\ExpenseDebt;
use App\Domain\Entity\Group;
use App\Models\Debtor;
use App\Models\Payer;
use Illuminate\Support\Facades\DB;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use Illuminate\Support\Facades\Log;

class EloquentGroupWriteRepository implements GroupWriteRepositoryInterface
{
    /**
     * @param Group $group
     *
     * @return void
     */
    public function save(Group $group): void
    {
        DB::transaction(function () use ($group) {
            $updated = $group->id !== null;

            $eloquentGroup = \App\Models\Group::updateOrCreate(
                ['id' => $group->id],
                [
                    'name'           => $group->name,
                    'category'       => $group->category,
                    'created_by'     => $group->createdBy,
                    'final_currency' => $group->finalCurrency,
                    'avatar'         => $group->avatar,
                ]
            );

            $group->id = (string)$eloquentGroup->id;
            $eloquentGroup->members()->sync(array_values(array_map(
                fn(Customer $customer) => $customer->id,
                $group->getMembers()
            )));

            $idsToDelete = array_map(
                fn (Expense $expense) => $expense->id,
                $group->getExpensesToDelete()
            );

            if (!empty($idsToDelete)) {
                \App\Models\Expense::query()->whereIn('id', $idsToDelete)->delete();
            }

            foreach ($group->getExpenses() as $expense) {
                $eloquentExpense = $eloquentGroup->expenses()->updateOrCreate(
                    ['id' => $expense->id],
                    [
                        'description'    => $expense->description,
                        'category'       => $expense->category,
                        'final_currency' => $expense->currency,
                        'created_at'     => $expense->createdAt,
                    ]
                );

                $expense->id = $eloquentExpense->id;

                foreach ($expense->getDebts() as $debt) {
                    $eloquentDebt = ExpenseDebt::query()->updateOrCreate(
                        ['id' => $debt->id],
                        [
                            'amount'     => $debt->amount,
                            'currency'   => $debt->currency,
                            'from'       => $debt->from->id,
                            'to'         => $debt->to->id,
                            'expense_id' => $eloquentExpense->id,
                            'group_id'   => $eloquentGroup->id,
                        ]
                    );
                    $debt->id = $eloquentDebt->id;
                }

                foreach ($expense->getPayers() as $payer) {
                    Payer::query()->updateOrCreate(
                        ['id' => $payer->id],
                        [

                            'expense_id' => $eloquentExpense->id,
                            'amount'     => $payer->amount,
                            'currency'   => $payer->currency,
                            'payer_id'   => $payer->payerId,
                        ]
                    );
                }

                foreach ($expense->getDebtors() as $debtor) {
                    Debtor::query()->updateOrCreate(
                        ['id' => $debtor->id],
                        [
                            'expense_id' => $eloquentExpense->id,
                            'amount'     => $debtor->amount,
                            'debtor_id'  => $debtor->debtorId,
                            'currency'   => $debtor->currency,
                        ]
                    );
                }
            }

            if ($group->simplifyDebts) {
                $distributedDebts = $group->distributeDebts();
                $eloquentGroup->debts()->delete();

                $insert = [];
                foreach ($distributedDebts as $debt) {
                    $insert[] = [
                        'amount'   => $debt->amount,
                        'currency' => $debt->currency,
                        'from'     => $debt->from->id,
                        'to'       => $debt->to->id,
                        'group_id' => $group->id,
                    ];
                }
                ExpenseDebt::query()->insert($insert);
            }

            if ($updated) {
                GroupUpdatedEvent::dispatch($group->id);
            }
        });
    }

    /**
     * @param Group $group
     *
     * @return bool
     */
    public function remove(Group $group): bool
    {
        $eloquentGroup = \App\Models\Group::find($group->id);
        if ($eloquentGroup) {
            return $eloquentGroup->delete();
        }

        return false;
    }
}
