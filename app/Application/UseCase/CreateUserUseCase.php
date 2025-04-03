<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use Illuminate\Support\Facades\DB;
use App\Infrastrstructure\API\DTO\CreateUserDTO;
use App\Infrastrstructure\Service\PasswordEncoder;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use App\Infrastrstructure\Mapper\CreateCustomerDTOToDomainEntity;

readonly class CreateUserUseCase
{
    /**
     * @param PasswordEncoder $passwordEncoder
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     * @param SendConfirmationCodeToCustomerUseCase $sendConfirmationCodeToCustomerUseCase
     */
    public function __construct(
        private PasswordEncoder $passwordEncoder,
        private CustomerWriteRepositoryInterface $customerWriteRepository,
        private SendConfirmationCodeToCustomerUseCase $sendConfirmationCodeToCustomerUseCase
    ) {}

    /**
     * @param CreateUserDTO $createUserDTO
     *
     * @return string
     */
    public function execute(CreateUserDTO $createUserDTO): string
    {
        return DB::transaction(function () use ($createUserDTO) {
            $createUserDTO->password = $this->passwordEncoder->hash($createUserDTO->password);

            $customerId = $this->customerWriteRepository->save(CreateCustomerDTOToDomainEntity::toEntity($createUserDTO));

            $this->sendConfirmationCodeToCustomerUseCase->execute($createUserDTO->email);

            return $this->customerWriteRepository->createToken($customerId);
        });
    }
}
