<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Transaction;
use App\Events\TransactionCreated;
use App\Infrastrstructure\API\DTO\DebtDTO;
use App\Domain\Listener\TransactionCreatedListener;
use App\Domain\Repository\DebtReadRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;

readonly class UpdateDebtUseCase
{
    public function __construct(
        private DebtReadRepositoryInterface $debtReadRepository,
        private TransactionWriteRepositoryInterface $transactionWriteRepository
    ) {}

    /**
     * @param int $id
     * @param DebtDTO $debtDTO
     *
     * @return void
     */
    public function execute(int $id, DebtDTO $debtDTO): void
    {
        $debt = $this->debtReadRepository->findById($id);

        if ($debt->amount < $debtDTO->amount || $debtDTO->amount < 0) {
            throw new \InvalidArgumentException('Invalid amount for debt update. The new amount must be less than or equal to the current amount and greater than or equal to zero.');
        }

        $transaction = $this->transactionWriteRepository->save(
            new Transaction(
                from    : $debt->from,
                to      : $debt->to,
                amount  : $debtDTO->amount,
                currency: $debt->currency,
                groupId : $debt->groupId,
            )
        );

        TransactionCreated::dispatch($transaction);
    }
}
