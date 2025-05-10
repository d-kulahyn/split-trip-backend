<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\Customer;
use App\Domain\Repository\CustomerWriteRepositoryInterface;

class EloquentCustomerWriteRepository implements CustomerWriteRepositoryInterface
{
    /**
     * @param Customer $customer
     *
     * @return int|null
     */
    public function save(Customer $customer): ?int
    {
        $eloquentCustomer = \App\Models\Customer::find($customer->id);

        if (!$eloquentCustomer) {
            $eloquentCustomer = new \App\Models\Customer();
        }

        $eloquentCustomer->fill([
            'password'                       => $customer->password,
            'email'                          => $customer->email,
            'name'                           => $customer->name,
            'social_id'                      => $customer->social_id,
            'avatar'                         => $customer->avatar,
            'social_type'                    => $customer->social_type,
            'email_verified_at'              => $customer->email_verified_at,
            'firebase_cloud_messaging_token' => $customer->firebase_cloud_messaging_token,
            'push_notifications'             => $customer->push_notifications,
            'email_notifications'            => $customer->email_notifications,
            'debt_reminder_period'           => $customer->debt_reminder_period->value,
            'avatar_color'                   => $customer->avatar_color,
        ]);

        $eloquentCustomer->friends()->attach($customer->friends);

        $eloquentCustomer->save();

        $customer->id = $eloquentCustomer->id;

        return $eloquentCustomer->id;
    }

    public function createToken(int $customerId): string
    {
        return \App\Models\Customer::find($customerId)->createToken('email')->plainTextToken;
    }

    public function removeTokens(int $customerId): void
    {
        $customer = \App\Models\Customer::find($customerId);

        $customer->tokens()->delete();
    }
}
