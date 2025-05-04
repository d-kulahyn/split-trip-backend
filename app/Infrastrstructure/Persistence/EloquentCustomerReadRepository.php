<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Customer;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Infrastrstructure\Persistence\Mappers\EloquentCustomerMapper;
use App\Infrastrstructure\Persistence\Mappers\EloquentGroupMapper;
use App\Models\Group;
use Illuminate\Support\Collection;

class EloquentCustomerReadRepository implements CustomerReadRepositoryInterface
{
    /**
     * @param string $field
     * @param string $login
     *
     * @return Customer|null
     */
    public function findByLogin(string $field, string $login): ?Customer
    {
        $customer = \App\Models\Customer::where($field, $login)->first();

        if (!$customer) {
            return null;
        }

        return EloquentCustomerMapper::map($customer);
    }

    /**
     * @param string $email
     *
     * @return Customer|null
     */
    public function findByEmail(string $email): ?Customer
    {
        $customer = \App\Models\Customer::where('email', $email)->first();

        if (!$customer) {
            return null;
        }

        return EloquentCustomerMapper::map($customer);
    }

    /**
     * @param array $ids
     * @param array $with
     *
     * @return Customer|null
     */
    public function findById(array $ids, array $with = []): ?Collection
    {
        $customer = \App\Models\Customer::with($with)->whereKey($ids)->get();

        if ($customer->isEmpty()) {
            return null;
        }

        return $customer->mapWithKeys(function ($customer) {
            return [$customer->id => EloquentCustomerMapper::map($customer)];
        });
    }

    /**
     * @param string $id
     * @param string $social
     *
     * @return Customer|null
     */
    public function getBySocialId(string $id, string $social): ?Customer
    {
        $customer = \App\Models\Customer::where('social_id', $id)
            ->where('social_type', $social)
            ->first();

        if (!$customer) {
            return null;
        }

        return EloquentCustomerMapper::map($customer);
    }

    /**
     * @param int $customerId
     * @param array $with
     *
     * @return Collection
     */
    public function groups(int $customerId, array $with = ['members', 'expenses']): Collection
    {
        $groups = Group::query()
            ->where('created_by', $customerId)
            ->orWhereHas('members', function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->with($with)
            ->get();

        return $groups->map(function ($group) {
            return EloquentGroupMapper::map($group);
        });
    }

    /**
     * @param array $customerId
     * @param array $friendId
     *
     * @return Collection|null
     */
    public function getCustomersWithoutSpecificFriends(array $customerId, array $friendId): ?Collection
    {
        $customers = \App\Models\Customer::whereKey($customerId)
            ->whereDoesntHave('friends', function ($query) use ($friendId) {
                $query->whereKey($friendId);
            })->get();

        if ($customers->isEmpty()) {
            return null;
        }

        return $customers->map(function ($customer) {
            return EloquentCustomerMapper::map($customer);
        });
    }
}
