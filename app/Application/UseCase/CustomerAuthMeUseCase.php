<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Customer;
use Illuminate\Support\Facades\Storage;
use App\Domain\Repository\CustomerReadRepositoryInterface;

readonly class CustomerAuthMeUseCase
{
    /**
     * @param CustomerReadRepositoryInterface $customerReadRepository
     */
    public function __construct(
        private CustomerReadRepositoryInterface $customerReadRepository,
    ) {}

    /**
     * @param int $customerId
     *
     * @return array
     */
    public function execute(int $customerId): array
    {
        /** @var Customer $customer */
        $customer = $this->customerReadRepository->findById([$customerId])->first();

        return [
            'id'                             => $customer->id,
            'name'                           => $customer->name,
            'email'                          => $customer->email,
            'currency'                       => $customer->currency,
            'avatar'                         => $customer->avatar !== null ? Storage::url($customer->avatar) : null,
            'balance'                        => $customer->getBalance()->toArray(),
            'email_is_verified'              => (bool)$customer->email_verified_at,
            'firebase_cloud_messaging_token' => $customer->firebase_cloud_messaging_token,
            'email_notifications'            => $customer->email_notifications,
            'push_notifications'             => $customer->push_notifications,
            'debt_reminder_period'           => $customer->debt_reminder_period,
        ];
    }
}
