<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Customer;
use App\Models\ExpenseDebt;
use App\Domain\Entity\Group;
use App\Models\ExpensePay;
use Illuminate\Support\Facades\DB;
use App\Domain\Repository\GroupWriteRepositoryInterface;

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

            foreach ($group->getExpenses() as $expense) {
                $res = $eloquentGroup->expenses()->updateOrCreate(
                    ['id' => $expense->id],
                    [
                        'description'    => $expense->description,
                        'category'       => $expense->category,
                        'final_currency' => $expense->currency,
                        'created_at'     => $expense->createdAt,
                    ]
                );

                $expense->id = $res->id;

                foreach ($expense->getDebts() as $debt) {
                    ExpenseDebt::query()->updateOrCreate(
                        ['id' => $debt->id],
                        [
                            'amount'     => $debt->amount,
                            'currency'   => $debt->currency,
                            'from'       => $debt->from,
                            'to'         => $debt->to,
                            'expense_id' => $res->id,
                        ]
                    );
                }

                foreach ($expense->getPays() as $pay) {
                    ExpensePay::query()->updateOrCreate(
                        ['id' => $pay->id],
                        [

                            'expense_id' => $res->id,
                            'amount'     => $pay->amount,
                            'currency'   => $pay->currency,
                            'payer_id'   => $pay->payerId,
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
                        'amount'     => $debt->amount,
                        'currency'   => $debt->currency,
                        'from'       => $debt->from,
                        'to'         => $debt->to,
                        'expense_id' => $res->id,
                    ];
                }
                ExpenseDebt::query()->insert($insert);
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
