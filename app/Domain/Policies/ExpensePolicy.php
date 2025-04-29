<?php

declare(strict_types=1);

namespace App\Domain\Policies;

use App\Models\Customer;
use App\Models\Expense;

class ExpensePolicy
{
    /**
     * @param Customer $customer
     * @param Expense $expense
     *
     * @return bool
     */
    public function delete(Customer $customer, Expense $expense): bool
    {
//        return $customer->id === $expense->created_by;
        return true;
    }
}
