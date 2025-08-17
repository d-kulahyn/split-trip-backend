<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\ActivityLog;
use App\Domain\Entity\Group;
use App\Domain\Entity\Payer;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Expense;
use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Domain\Repository\BalanceWriteRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Events\ActivityCreated;
use App\Infrastrstructure\API\Resource\ActivityResource;
use Illuminate\Support\Facades\DB;
use App\Infrastrstructure\API\DTO\ExpenseDTO;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\Service\CurrencyConverterService;
use App\Infrastrstructure\API\Exceptions\UnauthorizedGroupActionException;

readonly class AddExpenseUseCase
{
    /**
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     * @param CurrencyConverterService $currencyConverterService
     * @param BalanceWriteRepositoryInterface $balanceWriteRepository
     * @param ActivityWriteRepositoryInterface $activityWriteRepository
     * @param CustomerReadRepositoryInterface $customerReadRepository
     */
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository,
        private GroupWriteRepositoryInterface $groupWriteRepository,
        private CurrencyConverterService $currencyConverterService,
        private BalanceWriteRepositoryInterface $balanceWriteRepository,
        private ActivityWriteRepositoryInterface $activityWriteRepository,
        private CustomerReadRepositoryInterface $customerReadRepository,
    ) {}

    /**
     * @param ExpenseDTO $expenseDTO
     * @param string $groupId
     * @param int $customerId
     *
     * @throws UnauthorizedGroupActionException
     *
     * @return Group
     */
    public function execute(ExpenseDTO $expenseDTO, string $groupId, int $customerId): Group
    {
        $result = DB::transaction(function () use ($expenseDTO, $groupId, $customerId) {
            $group = $this->groupReadRepository->findById($groupId);

            if (!$group->hasMember($customerId)) {
                return false;
            }

            $expense = new Expense(
                category   : $expenseDTO->category,
                createdAt  : $expenseDTO->created_at,
                currency   : $expenseDTO->currency,
                description: $expenseDTO->description,
                groupId    : $group->id,
            );

            foreach ($expenseDTO->debtors as $debtor) {
                $expense->addDebtor(new Debtor(
                    amount  : $debtor->amount,
                    debtorId: $debtor->id,
                    currency: $expenseDTO->currency,
                ));
            }

            foreach ($expenseDTO->payers as $payer) {
                $expense->addPayer(new Payer(
                    amount  : $payer->amount,
                    currency: $payer->currency,
                    payerId : $payer->id
                ));
            }

            $expense->distributeDebts(
                $this->currencyConverterService,
                $group->finalCurrency
            );
            $group->addExpense($expense);

            $this
                ->groupWriteRepository
                ->save($group);

            $this->balanceWriteRepository->update(
                $group->getBalances(),
                $group->getMember($customerId),
            );

            //TODO: generate event for expense creation
            $activityLog = $this->activityWriteRepository->save(new ActivityLog(
                customerId: $customerId,
                groupId   : $groupId,
                groupName : $group->name,
                actionType: ActivityLogActionTypeEnum::EXPENSE_ADDED_TO_GROUP,
                customer  : $this->customerReadRepository->findById([$customerId])->first(),
                details   : [
                    'amount' => $expense->credits()
                ]
            ));

            foreach ($group->getMemberIds->reject(fn(int $id) => $id === $customerId)->toArray() as $memberId) {
                ActivityCreated::dispatch($memberId, new ActivityResource($activityLog));
            }

            return $group;
        });

        if (!$result) {
            throw new UnauthorizedGroupActionException('You are not allowed to add expenses.');
        }

        return $result;
    }
}
