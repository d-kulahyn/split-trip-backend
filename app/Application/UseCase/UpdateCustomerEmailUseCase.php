<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Mail\VerifyEmail;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use App\Infrastrstructure\API\DTO\UpdateCustomerEmailDTO;
use App\Infrastrstructure\Service\Interface\SecurityCodeStorageInterface;
use Illuminate\Support\Facades\Mail;

readonly class UpdateCustomerEmailUseCase
{
    /**
     * @param SecurityCodeStorageInterface $securityCodeGenerator
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     */
    public function __construct(
        private SecurityCodeStorageInterface $securityCodeGenerator,
        private CustomerReadRepositoryInterface $customerReadRepository,
        private CustomerWriteRepositoryInterface $customerWriteRepository
    ) {}

    /**
     * @param int $id
     * @param UpdateCustomeremailDTO $updateCustomerEmailDTO
     *
     * @return void
     */
    public function execute(int $id, UpdateCustomeremailDTO $updateCustomerEmailDTO): void
    {
        $customer = $this->customerReadRepository->findById([$id])->first();

        $customer->email = $updateCustomerEmailDTO->email;
        $customer->email_verified_at = null;

        $this->customerWriteRepository->save($customer);

        $code = $this->securityCodeGenerator->set($id);

        Mail::to($customer->email)->queue(new VerifyEmail($code));
    }
}
