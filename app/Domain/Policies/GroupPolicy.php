<?php

declare(strict_types=1);

namespace App\Domain\Policies;

use App\Models\Group;
use App\Models\Customer;

class GroupPolicy
{
    /**
     * @param Customer $customer
     * @param Group $group
     *
     * @return bool
     */
    public function update(Customer $customer, Group $group): bool
    {
        return $customer->id === $group->created_by;
    }
}
