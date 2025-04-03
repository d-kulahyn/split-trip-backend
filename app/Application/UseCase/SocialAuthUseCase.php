<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Customer;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use App\Infrastrstructure\API\DTO\SocialAuthDTO;
use App\Infrastrstructure\Service\PasswordEncoder;
use App\Infrastrstructure\Service\PasswordGenerator;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Random\RandomException;

readonly class SocialAuthUseCase
{
    /**
     * @param PasswordEncoder $passwordEncoder
     * @param PasswordGenerator $passwordGeneratorService
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     */
    public function __construct(
        protected PasswordEncoder $passwordEncoder,
        protected PasswordGenerator $passwordGeneratorService,
        protected CustomerReadRepositoryInterface $customerReadRepository,
        protected CustomerWriteRepositoryInterface $customerWriteRepository
    ) {}

    /**
     * @throws RandomException
     */
    public function execute(SocialAuthDTO $socialAuthDTO, string $social): string
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($social);
        $socialUser = $driver->userFromToken($socialAuthDTO->access_token);
        $customer = $this->customerReadRepository->getBySocialId($socialUser->getId(), $social);
        if (is_null($customer)) {
            $customer = new Customer(
                password   : $this->passwordEncoder->hash($this->passwordGeneratorService->generate()),
                email      : $socialUser->getEmail(),
                social_type: $social,
                social_id  : $socialUser->getId(),
            );
            $this->customerWriteRepository->save($customer);
        }

        return $this->customerWriteRepository->createToken($customer->id);
    }
}
