<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

readonly class ConfirmEmailUseCase
{
    /**
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     */
    public function __construct(
        protected CustomerReadRepositoryInterface $customerReadRepository,
        protected CustomerWriteRepositoryInterface $customerWriteRepository
    ) {}

    /**
     * @param int $customerId
     *
     * @return void
     */
    public function execute(int $customerId): void
    {
        $customer = $this->customerReadRepository->findById([$customerId])->first();

        if (is_null($customer)) {
            throw new BadRequestException('Customer not found');
        }

        $customer->email_verified_at = now()->format('Y-m-d H:i:s');

        $this->customerWriteRepository->save($customer);
    }
}
