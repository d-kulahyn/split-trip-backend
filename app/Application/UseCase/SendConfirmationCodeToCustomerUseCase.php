<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Mail\VerifyEmail;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Infrastrstructure\Service\Interface\SecurityCodeStorageInterface;
use Illuminate\Support\Facades\Mail;

readonly class SendConfirmationCodeToCustomerUseCase
{
    /**
     * @param SecurityCodeStorageInterface $securityCodeGenerator
     * @param CustomerReadRepositoryInterface $customerReadRepository
     */
    public function __construct(
        private SecurityCodeStorageInterface $securityCodeGenerator,
        private CustomerReadRepositoryInterface $customerReadRepository
    ) {}

    /**
     * @param string $email
     *
     * @return void
     */
    public function execute(string $email): void
    {
        $customer = $this->customerReadRepository->findByEmail($email);

        $code = $this->securityCodeGenerator->set($customer->id);

        Mail::to($email)->queue(new VerifyEmail($code));
    }
}
