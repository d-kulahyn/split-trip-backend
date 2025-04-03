<?php

declare(strict_types=1);

namespace App\Domain\Policies;

use App\Models\Customer;
use App\Models\ExpenseDebt;

class DebtPolicy
{
    /**
     * @param Customer $customer
     * @param ExpenseDebt $debt
     *
     * @return bool
     */
    public function update(Customer $customer, ExpenseDebt $debt): bool
    {
        return $customer->id === $debt->from;
    }
}
