<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Payer;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Expense;
use Illuminate\Support\Facades\DB;
use App\Infrastrstructure\API\DTO\ExpenseDTO;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\Service\CurrencyConverterService;
use App\Infrastrstructure\API\Exceptions\UnauthorizedGroupActionException;

readonly class UpdateExpenseUseCase
{
    /**
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     * @param CurrencyConverterService $currencyConverterService
     */
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository,
        private GroupWriteRepositoryInterface $groupWriteRepository,
        private CurrencyConverterService $currencyConverterService,
    ) {}

    /**
     * @param ExpenseDTO $expenseDTO
     * @param string $groupId
     * @param int $expenseId
     * @param int $customerId
     *
     * @throws UnauthorizedGroupActionException
     * @return Expense
     */
    public function execute(ExpenseDTO $expenseDTO, string $groupId, int $expenseId, int $customerId): Expense
    {
        $result = DB::transaction(function () use ($expenseDTO, $groupId, $customerId, $expenseId) {
            $group = $this->groupReadRepository->findById($groupId, lock: true);

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

            $group->removeExpense($expenseId);
            $group->addExpense($expense);

            $expense->distributeDebts($this->currencyConverterService, $group->finalCurrency);

            $this->groupWriteRepository->save($group);

            return $expense;
        });

        if (!$result) {
            throw new UnauthorizedGroupActionException('You are not allowed to add expenses.');
        }

        return $result;
    }
}
