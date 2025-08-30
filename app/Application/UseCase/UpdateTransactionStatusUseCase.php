<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\ActivityLog;
use App\Domain\Entity\Customer;
use App\Domain\Entity\Group;
use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Enum\StatusEnum;
use App\Domain\Event\ActivityCreatedEvent;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

readonly class UpdateTransactionStatusUseCase
{

    public function __construct(
        private TransactionReadRepositoryInterface $transactionReadRepository,
        private TransactionWriteRepositoryInterface $transactionWriteRepository,
        private ActivityWriteRepositoryInterface $activityLogRepository,
        private CustomerReadRepositoryInterface $customerReadRepository,
        private GroupWriteRepositoryInterface $groupWriteRepository,
        private GroupReadRepositoryInterface $groupReadRepository
    ) {}

    public function execute(int $transactionId, StatusEnum $statusEnum, int $authId): void
    {
        $transaction = $this->transactionReadRepository->getById($transactionId);

        if (!$transaction) {
            throw new InvalidArgumentException('Transaction not found');
        }

        $transaction->status = $statusEnum;

        /** @var Group $group */
        $group = $this->groupReadRepository->findById($transaction->groupId);

        DB::transaction(function () use ($transaction, $statusEnum, $authId, $group) {
            $this->transactionWriteRepository->save($transaction);

            if ($transaction->status == StatusEnum::PAID) {
                $debt = $group->getDebtBetween($transaction->from->id, $transaction->to->id);

                if (!$debt) {
                    throw new InvalidArgumentException('Debt not found');
                }

                $group->updateDebtAmount($debt, $transaction->amount, $transaction->currency);
                $this->groupWriteRepository->save($group);
            }

            /** @var Customer $createdBy */
            $createdBy = $this->customerReadRepository->findById([$authId])->first();
            $activityLog = $this->activityLogRepository->save(new ActivityLog(
                groupId    : $transaction->groupId,
                groupName  : $transaction->groupName,
                actionType : ActivityLogActionTypeEnum::TRANSACTION_UPDATED,
                customerIds: $transaction->group->getMemberIds(),
                createdBy  : $createdBy,
                status     : StatusEnum::PENDING,
                details    : [
                    'transaction_id' => $transaction->id,
                    'status'         => $statusEnum->value,
                ]
            ));

            ActivityCreatedEvent::dispatch($transaction->from->id, $activityLog);
        });
    }
}
