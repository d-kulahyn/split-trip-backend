<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DebtException;
use App\Domain\Entity\Debt;
use App\Domain\Entity\Transaction;
use App\Domain\Enum\DebtStatusEnum;
use App\Domain\Repository\DebtReadRepositoryInterface;
use App\Domain\Repository\DebtWriteRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use App\Infrastrstructure\API\DTO\DebtDTO;

readonly class UpdateDebtUseCase
{
    /**
     * @param DebtReadRepositoryInterface $debtReadRepository
     * @param DebtWriteRepositoryInterface $debtWriteRepository
     * @param TransactionWriteRepositoryInterface $transactionWriteRepository
     */
    public function __construct(
        private DebtReadRepositoryInterface $debtReadRepository,
        private DebtWriteRepositoryInterface $debtWriteRepository,
        private TransactionWriteRepositoryInterface $transactionWriteRepository
    ) {}

    /**
     * @param int $id
     * @param DebtDTO $debtDTO
     *
     * @throws DebtException
     * @return void
     */
    public function execute(int $id, DebtDTO $debtDTO): void
    {
        /** @var Debt $debt */
        $debt = $this->debtReadRepository->findById($id);

        if ($debtDTO->amount <= 0 || $debtDTO->amount > $debt->amount) {
            throw new DebtException('Invalid amount provided.');
        }

        $debt->amount = (float)bcsub((string)$debt->amount, (string)$debtDTO->amount, 2);

        if ($debt->amount === 0.00) {
            $debt->status = DebtStatusEnum::PAID;
        }

        $this->transactionWriteRepository->save(
            new Transaction(
                from    : $debt->from,
                to      : $debt->to,
                amount  : $debtDTO->amount,
                currency: $debt->currency,
                groupId : $debt->groupId,
            )
        );

        $this->debtWriteRepository->save($debt);
    }
}
