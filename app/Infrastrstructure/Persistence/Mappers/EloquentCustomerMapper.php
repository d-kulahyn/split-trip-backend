<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence\Mappers;

use App\Models\Customer;

class EloquentCustomerMapper
{
    /**
     * @param Customer $customer
     *
     * @return \App\Domain\Entity\Customer
     */
    public static function map(Customer $customer): \App\Domain\Entity\Customer
    {
        $friends = $customer->relationLoaded('friends')
            ? $customer->friends->map(fn($friend) => $friend->friend_id)
            : [];

        return new \App\Domain\Entity\Customer(
            password                      : $customer->password,
            email                         : $customer->email,
            friends                       : $friends,
            email_notifications           : $customer->email_notifications,
            push_notifications            : $customer->push_notifications,
            debt_reminder_period          : $customer->debt_reminder_period,
            firebase_cloud_messaging_token: $customer->firebase_cloud_messaging_token,
            social_type                   : $customer->social_type,
            social_id                     : $customer->social_id,
            email_verified_at             : $customer->email_verified_at?->format('Y-m-d H:i:s'),
            name                          : $customer->name,
            avatar                        : $customer->avatar,
            id                            : $customer->id,
            currency                      : $customer->currency,

        );
    }
}
