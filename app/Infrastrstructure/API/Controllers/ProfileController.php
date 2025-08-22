<?php

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\CustomerAuthMeUseCase;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use Illuminate\Http\JsonResponse;
use App\Application\UseCase\UpdateCustomerEmailUseCase;
use App\Infrastrstructure\API\DTO\UpdateCustomerEmailDTO;
use App\Application\UseCase\UpdateCustomerPasswordUseCase;
use App\Application\UseCase\UpdateCustomerUseCase;
use App\Infrastrstructure\API\DTO\UpdateCustomerDTO;
use App\Infrastrstructure\API\DTO\UpdateCustomerPasswordDTO;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

readonly class ProfileController
{
    /**
     * @param UpdateCustomerUseCase $updateCustomerUseCase
     * @param UpdateCustomerEmailUseCase $updateCustomerEmailUseCase
     * @param UpdateCustomerPasswordUseCase $updateCustomerPasswordUseCase
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param CustomerWriteRepositoryInterface $customerWriteRepository
     * @param CustomerAuthMeUseCase $customerAuthMeUseCase
     */
    public function __construct(
        private UpdateCustomerUseCase $updateCustomerUseCase,
        private UpdateCustomerEmailUseCase $updateCustomerEmailUseCase,
        private UpdateCustomerPasswordUseCase $updateCustomerPasswordUseCase,
        private CustomerReadRepositoryInterface $customerReadRepository,
        private CustomerWriteRepositoryInterface $customerWriteRepository,
        private CustomerAuthMeUseCase $customerAuthMeUseCase,
    ) {}

    /**
     * @param UpdateCustomerDTO $updateCustomerDTO
     *
     * @return JsonResponse
     */
    public function update(UpdateCustomerDTO $updateCustomerDTO): JsonResponse
    {
        $this->updateCustomerUseCase->execute(request()->user()->id, $updateCustomerDTO);

        return response()->json($this->customerAuthMeUseCase->execute(request()->user()->id));
    }

    /**
     * @param UpdateCustomerPasswordDTO $updateCustomerPasswordDTO
     *
     * @return JsonResponse
     */
    public function updatePassword(UpdateCustomerPasswordDTO $updateCustomerPasswordDTO): JsonResponse
    {
        $this->updateCustomerPasswordUseCase->execute(request()->user()->id, $updateCustomerPasswordDTO);

        return response()->json(['message' => 'Password has been updated successfully.']);
    }

    /**
     * @param UpdateCustomerEmailDTO $updateCustomerEmailDTO
     *
     * @return JsonResponse
     */
    public function updateEmail(UpdateCustomerEmailDTO $updateCustomerEmailDTO): JsonResponse
    {
        $this->updateCustomerEmailUseCase->execute(request()->user()->id, $updateCustomerEmailDTO);

        return response()->json(['message' => 'Email has been updated successfully.']);
    }

    /**
     * @return JsonResponse
     */
    public function uploadAvatar(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], Response::HTTP_BAD_REQUEST);
        }

        $file = request()->file('avatar');

        $path = $file->storeAs('customers/avatars', request()->user()->id . '.' . $file->extension());

        $customer = $this->customerReadRepository->findById([request()->user()->id])->first();

        $customer->avatar = $path;

        $this->customerWriteRepository->save($customer);

        return response()->json(['path' => Storage::url($path)]);
    }

    /**
     * @return JsonResponse
     */
    public function setCloudMessagingToken(): JsonResponse
    {
        request()->validate([
            'token' => 'required|string'
        ]);

        $customer = $this->customerReadRepository->findById([request()->user()->id])->first();

        $customer->firebase_cloud_messaging_token = request()->input('token');

        $this->customerWriteRepository->save($customer);

        return response()->json(['message' => 'Token has been updated successfully.']);
    }
}
