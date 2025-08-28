<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Transaction;
use App\Domain\Event\TransactionCreated;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\DebtDTO;
use App\Domain\Repository\DebtReadRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use Google\Cloud\Core\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class UpdateDebtUseCase
{
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository,
        private DebtReadRepositoryInterface $debtReadRepository,
        private TransactionWriteRepositoryInterface $transactionWriteRepository,
        private TransactionReadRepositoryInterface $transactionReadRepository
    ) {}

    /**
     * @param int $id
     * @param DebtDTO $debtDTO
     * @param string $groupId
     *
     * @return Transaction
     */
    public function execute(int $id, DebtDTO $debtDTO, string $groupId): Transaction
    {
        $debt = $this->debtReadRepository->findById($id);

        $pendingAmount = $this->transactionReadRepository->getPendingAmount($debt->to->id);

        if ($pendingAmount + $debtDTO->amount > $debt->amount) {
            throw new BadRequestHttpException('Amount is out of range');
        }

        if ($debt->amount < $debtDTO->amount || $debtDTO->amount < 0) {
            throw new BadRequestHttpException('Invalid amount');
        }

        $transaction = $this->transactionWriteRepository->save(
            new Transaction(
                from    : $debt->from,
                to      : $debt->to,
                amount  : $debtDTO->amount,
                currency: $debt->currency,
                groupId : $debt->groupId,
                groupName: $this->groupReadRepository->getNameById($groupId),
            )
        );

        TransactionCreated::dispatch($transaction, $groupId);

        return $transaction;
    }
}
