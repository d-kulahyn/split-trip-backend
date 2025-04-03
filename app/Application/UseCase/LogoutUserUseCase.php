<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\CustomerWriteRepositoryInterface;

readonly class LogoutUserUseCase
{
    /**
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     */
    public function __construct(
        private CustomerWriteRepositoryInterface $customerWriteRepository
    ) {}

    /**
     * @param int $customerId
     *
     * @return void
     */
    public function execute(int $customerId): void
    {
        $this->customerWriteRepository->removeTokens($customerId);
    }
}
