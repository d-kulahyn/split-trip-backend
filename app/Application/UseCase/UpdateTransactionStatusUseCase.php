<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\ActivityLog;
use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use App\Events\ActivityCreated;
use App\Infrastrstructure\API\Resource\ActivityResource;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

readonly class UpdateTransactionStatusUseCase
{

    public function __construct(
        private TransactionReadRepositoryInterface $transactionReadRepository,
        private TransactionWriteRepositoryInterface $transactionWriteRepository,
        private ActivityWriteRepositoryInterface $activityLogRepository
    ) {}

    public function execute(int $transactionId, StatusEnum $statusEnum): void
    {
        $transaction = $this->transactionReadRepository->getById($transactionId);

        if (!$transaction) {
            throw new InvalidArgumentException('Transaction not found');
        }

        $transaction->status = $statusEnum;

        DB::transaction(function () use ($transaction, $statusEnum) {
            $this->transactionWriteRepository->save($transaction);

            $activityLog = $this->activityLogRepository->save(new ActivityLog(
                customerId: $transaction->from->id,
                groupId   : $transaction->groupId,
                groupName : $transaction->groupName,
                actionType: ActivityLogActionTypeEnum::TRANSACTION_UPDATED,
                details   : [
                    'transaction_id' => $transaction->id,
                    'status'         => $statusEnum->value,
                    'to'             => $transaction->to->id,
                ],
            ));

            ActivityCreated::dispatch($transaction->from->id, new ActivityResource($activityLog));
        });
    }
}
