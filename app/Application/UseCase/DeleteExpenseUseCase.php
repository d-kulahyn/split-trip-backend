<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Expense;
use App\Domain\Enum\ActivityLogActionTypeEnum;
use Illuminate\Support\Facades\DB;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;

readonly class DeleteExpenseUseCase
{
    /**
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     * @param LogActivityUseCase $logActivityUseCase
     */
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository,
        private GroupWriteRepositoryInterface $groupWriteRepository,
        private LogActivityUseCase $logActivityUseCase
    ) {}

    /**
     * @param string $groupId
     * @param int $expenseId
     * @param int $customerId
     *
     * @return Expense
     */
    public function execute(string $groupId, int $expenseId, int $customerId): Expense
    {
        return DB::transaction(function () use ($groupId, $expenseId, $customerId) {
            $group = $this->groupReadRepository->findById($groupId);

            $group->removeExpense($expenseId);

            $this->groupWriteRepository->save($group);

            $this->logActivityUseCase->execute(
                $customerId,
                $group->id,
                ActivityLogActionTypeEnum::EXPENSE_ADDED_TO_GROUP,
                ['expense_id' => $expenseId]
            );

            return true;
        });
    }
}
