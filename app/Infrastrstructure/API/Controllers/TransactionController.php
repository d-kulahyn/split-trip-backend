<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\UpdateTransactionStatusUseCase;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\UpdateTransactionStatusDTO;
use App\Infrastrstructure\API\Resource\GroupResource;
use App\Infrastrstructure\API\Resource\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

readonly class TransactionController
{

    public function __construct(
        private UpdateTransactionStatusUseCase $updateTransactionStatusUseCase,
        private TransactionReadRepositoryInterface $transactionReadRepository,
        private GroupReadRepositoryInterface $groupReadRepository
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TransactionResource::collection($this->transactionReadRepository->list(StatusEnum::PENDING, auth()->id()));
    }

    public function status(Transaction $transaction, UpdateTransactionStatusDTO $updateTransactionStatusDTO): GroupResource
    {
        $this->updateTransactionStatusUseCase->execute($transaction->id, $updateTransactionStatusDTO->status, auth()->id());

        return new GroupResource($this->groupReadRepository->findById($transaction->group_id));
    }
}
