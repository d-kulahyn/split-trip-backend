<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\UpdateDebtUseCase;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\DebtDTO;
use App\Infrastrstructure\API\Resource\GroupResource;
use App\Infrastrstructure\API\Resource\TransactionResource;
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
     * @return Response
     */
    public function update(ExpenseDebt $debt, DebtDTO $debtDTO): Response
    {
        $transaction = $this->updateDebtStatusUseCase->execute($debt->id, $debtDTO, $debt->group_id);

        return response([
            "group"       => new GroupResource($this->groupReadRepository->findById($debt->group_id)),
            "transaction" => new TransactionResource($transaction),
        ], ResponseAlias::HTTP_CREATED);
    }
}
