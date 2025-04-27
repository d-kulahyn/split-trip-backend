<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\UpdateDebtUseCase;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\DebtDTO;
use App\Infrastrstructure\API\Resource\GroupResource;
use App\Models\ExpenseDebt;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

readonly class DebtsController
{
    /**
     * @param UpdateDebtUseCase $updateDebtStatusUseCase
     * @param GroupReadRepositoryInterface $groupReadRepository
     */
    public function __construct(
        protected UpdateDebtUseCase $updateDebtStatusUseCase,
        protected GroupReadRepositoryInterface $groupReadRepository,
    ) {}

    /**
     * @param ExpenseDebt $debt
     * @param DebtDTO $debtDTO
     *
     * @throws \App\Application\DebtException
     * @return Response
     */
    public function update(ExpenseDebt $debt, DebtDTO $debtDTO): Response
    {
        $this->updateDebtStatusUseCase->execute($debt->id, $debtDTO);

        return response(new GroupResource($this->groupReadRepository->findById($debt->group_id)), ResponseAlias::HTTP_CREATED);
    }
}
