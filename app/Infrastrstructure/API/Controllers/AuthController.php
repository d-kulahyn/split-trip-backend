<?php

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\ConfirmEmailUseCase;
use App\Application\UseCase\CreateUserUseCase;
use App\Application\UseCase\CustomerAuthMeUseCase;
use App\Application\UseCase\LoginUserUseCase;
use App\Application\UseCase\LogoutUserUseCase;
use App\Application\UseCase\ResetPasswordUseCase;
use App\Application\UseCase\SendConfirmationCodeToCustomerUseCase;
use App\Application\UseCase\SocialAuthUseCase;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\ActivityReadRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\ConfirmEmailDTO;
use App\Infrastrstructure\API\DTO\CreateUserDTO;
use App\Infrastrstructure\API\DTO\LoginDTO;
use App\Infrastrstructure\API\DTO\ResetPasswordDTO;
use App\Infrastrstructure\API\DTO\SocialAuthDTO;
use App\Infrastrstructure\API\Resource\ActivityResource;
use App\Infrastrstructure\API\Resource\GroupResource;
use App\Infrastrstructure\API\Resource\TransactionResource;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Http\JsonResponse;
use Random\RandomException;

readonly class AuthController
{
    /**
     * @param CreateUserUseCase $createUserUseCase
     * @param LoginUserUseCase $loginUserUseCase
     * @param LogoutUserUseCase $logoutUserUseCase
     * @param ResetPasswordUseCase $resetPasswordUseCase
     * @param ConfirmEmailUseCase $confirmEmailUseCase
     * @param SocialAuthUseCase $socialAuthUseCase
     * @param CustomerAuthMeUseCase $customerAuthMeUseCase
     * @param SendConfirmationCodeToCustomerUseCase $sendConfirmationCodeToCustomerUseCase
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param GroupReadRepositoryInterface $groupReadRepository
     */
    public function __construct(
        protected CreateUserUseCase $createUserUseCase,
        protected LoginUserUseCase $loginUserUseCase,
        protected LogoutUserUseCase $logoutUserUseCase,
        protected ResetPasswordUseCase $resetPasswordUseCase,
        protected ConfirmEmailUseCase $confirmEmailUseCase,
        protected SocialAuthUseCase $socialAuthUseCase,
        protected CustomerAuthMeUseCase $customerAuthMeUseCase,
        protected SendConfirmationCodeToCustomerUseCase $sendConfirmationCodeToCustomerUseCase,
        protected CustomerReadRepositoryInterface $customerReadRepository,
        protected GroupReadRepositoryInterface $groupReadRepository,
        protected TransactionReadRepositoryInterface $transactionReadRepository,
        protected ActivityReadRepositoryInterface $activityReadRepository,
    ) {}

    /**
     * @param CreateUserDTO $createUserDTO
     *
     * @return JsonResponse
     */
    public function register(CreateUserDTO $createUserDTO): JsonResponse
    {
        $token = $this->createUserUseCase->execute($createUserDTO);

        $customer = $this->customerReadRepository->findByEmail($createUserDTO->email);

        return response()->json([
            'access_token' => $token,
            'user'         => $this->customerAuthMeUseCase->execute($customer->id),
        ], ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param LoginDTO $loginDTO
     *
     * @return JsonResponse
     */
    public function login(LoginDTO $loginDTO): JsonResponse
    {
        $token = $this->loginUserUseCase->execute($loginDTO);
        if (is_null($token)) {
            return response()->json(['message' => 'Bad login or password.'], ResponseAlias::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'access_token' => $token,
            'user'         => $this->customerAuthMeUseCase->execute(request()->user()->id),
            'groups'       => GroupResource::collection($this->groupReadRepository->list(auth()->id())),
            'transactions' => TransactionResource::collection($this->transactionReadRepository->list(
                StatusEnum::PENDING,
                auth()->id()
            )),
            'activities'   => ActivityResource::collection(
                $this->activityReadRepository->list(
                    StatusEnum::PENDING,
                    auth()->id(),
                    ['group', 'customer']
                )
            ),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function sendConfirmationCode(): JsonResponse
    {
        $this->sendConfirmationCodeToCustomerUseCase->execute(request()->user()->email);

        return response()->json(['message' => 'Confirmation code has been sent']);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->logoutUserUseCase->execute(request()->user()->id);

        return response()->json(['message' => 'Token has been deleted successfully']);
    }

    /**
     * @param ResetPasswordDTO $resetPasswordDTO
     *
     * @throws RandomException
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordDTO $resetPasswordDTO): JsonResponse
    {
        $this->resetPasswordUseCase->execute($resetPasswordDTO->email);

        return response()->json(['message' => 'Email has been sent']);
    }

    /**
     * @param ConfirmEmailDTO $confirmEmailDTO
     *
     * @return JsonResponse
     */
    public function confirmEmail(ConfirmEmailDTO $confirmEmailDTO): JsonResponse
    {
        $this->confirmEmailUseCase->execute(request()->user()->id);

        return response()->json(['message' => 'Email has been verified']);
    }

    /**
     * @param SocialAuthDTO $socialAuthDTO
     * @param string $social
     *
     * @throws RandomException
     * @return JsonResponse
     */
    public function social(SocialAuthDTO $socialAuthDTO, string $social): JsonResponse
    {
        $token = $this->socialAuthUseCase->execute($socialAuthDTO, $social);

        return response()->json(['access_token' => $token]);
    }

    /**
     * @return array
     */
    public function me(): array
    {
        return $this->customerAuthMeUseCase->execute(request()->user()->id);
    }
}
