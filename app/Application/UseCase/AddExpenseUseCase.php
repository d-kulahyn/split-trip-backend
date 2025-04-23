<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Payer;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Expense;
use App\Domain\Enum\ActivityLogActionTypeEnum;
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
     * @param LogActivityUseCase $logActivityUseCase
     */
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository,
        private GroupWriteRepositoryInterface $groupWriteRepository,
        private CurrencyConverterService $currencyConverterService,
        private LogActivityUseCase $logActivityUseCase
    ) {}

    /**
     * @param ExpenseDTO $expenseDTO
     * @param string $groupId
     * @param int $customerId
     *
     * @throws UnauthorizedGroupActionException
     *
     * @return Expense
     */
    public function execute(ExpenseDTO $expenseDTO, string $groupId, int $customerId): Expense
    {
        $result = DB::transaction(function () use ($expenseDTO, $groupId, $customerId) {
            $group = $this->groupReadRepository->findById($groupId);

            if (!$group->hasMember($customerId)) {
                return false;
            }

            $expense = new Expense(
                category: $expenseDTO->category,
                createdAt: $expenseDTO->created_at,
                currency: $expenseDTO->currency,
                description: $expenseDTO->description,
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

            $expense->distributeDebts($this->currencyConverterService, $group->finalCurrency);

            $group->addExpense($expense);

            $this->groupWriteRepository->save($group);

            $this->logActivityUseCase->execute(
                $customerId,
                $group->id,
                ActivityLogActionTypeEnum::EXPENSE_ADDED_TO_GROUP,
                ['expense_id' => $expense->id]
            );

            return $expense;
        });

        if (!$result) {
            throw new UnauthorizedGroupActionException('You are not allowed to add expenses.');
        }

        return $result;
    }
}
