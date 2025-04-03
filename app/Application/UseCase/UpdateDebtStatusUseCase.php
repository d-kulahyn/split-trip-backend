<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Enum\DebtStatusEnum;
use App\Domain\Repository\DebtReadRepositoryInterface;
use App\Domain\Repository\DebtWriteRepositoryInterface;

readonly class UpdateDebtStatusUseCase
{
    /**
     * @param DebtReadRepositoryInterface $debtReadRepository
     * @param DebtWriteRepositoryInterface $debtWriteRepository
     */
    public function __construct(
        private DebtReadRepositoryInterface $debtReadRepository,
        private DebtWriteRepositoryInterface $debtWriteRepository,
    ) {}

    /**
     * @param int $id
     * @param DebtStatusEnum $status
     *
     * @return void
     */
    public function execute(int $id, DebtStatusEnum $status): void
    {
        $debt = $this->debtReadRepository->findById($id);

        $debt->status = $status;

        $this->debtWriteRepository->save($debt);
    }
}
