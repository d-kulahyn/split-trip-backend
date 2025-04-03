<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\LoginDTO;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use Illuminate\Support\Facades\Auth;

readonly class LoginUserUseCase
{
    /**
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     */
    public function __construct(
        private CustomerReadRepositoryInterface $customerReadRepository,
        private CustomerWriteRepositoryInterface $customerWriteRepository
    ) {}

    /**
     * @param LoginDTO $loginDTO
     * @param string $guard
     *
     * @return string|null
     */
    public function execute(LoginDTO $loginDTO, string $guard = 'web'): ?string
    {
        $credentials = [
            'email' => $loginDTO->email,
            'password'  => $loginDTO->password
        ];

        if (Auth::guard($guard)->attempt($credentials)) {
            $customer = $this->customerReadRepository->findByEmail($loginDTO->email);

            $this->customerWriteRepository->removeTokens($customer->id);

            return $this->customerWriteRepository->createToken($customer->id);
        }

        return null;
    }
}
