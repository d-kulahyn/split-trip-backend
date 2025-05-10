<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Mapper;


use App\Models\Customer;

class CustomerEloquentToDomainEntity
{
    /**
     * @param Customer $customerEloquent
     *
     * @return \App\Domain\Entity\Customer
     */
    public static function toEntity(Customer $customerEloquent): \App\Domain\Entity\Customer
    {
        $friends = [];

        foreach ($customerEloquent->friends as $friend) {
            $friends[] = new \App\Domain\Entity\Customer(
                $friend->password,
                $friend->email,
                [],
                $friend->email_notifications,
                $friend->push_notifications,
                $friend->debt_reminder_period,
                $friend->firebase_cloud_messaging_token,
                $friend->social_type,
                $friend->social_id,
                $friend->email_verified_at?->format('Y-m-d H:i:s'),
                $friend->name,
                $customerEloquent->avatar,
                $friend->id,
                $friend->currency,
                null,
                $friend->avatar_color,
            );
        }

        return new \App\Domain\Entity\Customer(
            $customerEloquent->password,
            $customerEloquent->email,
            $friends,
            $customerEloquent->email_notifications,
            $customerEloquent->push_notifications,
            $customerEloquent->debt_reminder_period,
            $customerEloquent->firebase_cloud_messaging_token,
            $customerEloquent->social_type,
            $customerEloquent->social_id,
            $customerEloquent->email_verified_at?->format('Y-m-d H:i:s'),
            $customerEloquent->name,
            $customerEloquent->avatar,
            $customerEloquent->id,
            $customerEloquent->currency,
            null,
            $customerEloquent->avatar_color,
        );
    }
}
