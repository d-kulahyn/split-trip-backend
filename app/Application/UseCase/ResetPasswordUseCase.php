<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Mail\ResetPasswordEmail;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use App\Infrastrstructure\Service\PasswordGenerator;
use Illuminate\Support\Facades\Mail;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

readonly class ResetPasswordUseCase
{
    /**
     * @param PasswordGenerator $passwordGenerator
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     */
    public function __construct(
        private PasswordGenerator $passwordGenerator,
        private CustomerReadRepositoryInterface $customerReadRepository,
        private CustomerWriteRepositoryInterface $customerWriteRepository
    ) {}

    /**
     * @throws RandomException
     */
    public function execute(string $email): true
    {
        $customer = $this->customerReadRepository->findByEmail($email);

        if (is_null($customer)) {
            throw new BadRequestException('Customer not found');
        }

        $customer->password = $this->passwordGenerator->generate();

        $this->customerWriteRepository->save($customer);

        Mail::to($customer->email)->queue(new ResetPasswordEmail($customer->password));

        return true;
    }
}
