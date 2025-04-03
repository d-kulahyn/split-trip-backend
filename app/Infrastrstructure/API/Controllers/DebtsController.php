<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\UpdateDebtStatusUseCase;
use App\Infrastrstructure\API\DTO\DebtStatusDTO;
use App\Models\ExpenseDebt;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class DebtsController
{
    /**
     * @param UpdateDebtStatusUseCase $updateDebtStatusUseCase
     */
    public function __construct(
        protected UpdateDebtStatusUseCase $updateDebtStatusUseCase
    ) {}

    /**
     * @param ExpenseDebt $debt
     * @param DebtStatusDTO $debtStatusDTO
     *
     * @return JsonResponse
     */
    public function changeStatus(ExpenseDebt $debt, DebtStatusDTO $debtStatusDTO): JsonResponse
    {
        $this->updateDebtStatusUseCase->execute($debt->id, $debtStatusDTO->status);

        return response()->json(['id' => 'Debt status updated successfully'], Response::HTTP_OK);
    }
}
