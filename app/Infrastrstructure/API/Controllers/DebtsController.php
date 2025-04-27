<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Application\UseCase\UpdateDebtUseCase;
use App\Infrastrstructure\API\DTO\DebtDTO;
use App\Models\ExpenseDebt;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class DebtsController
{
    /**
     * @param UpdateDebtUseCase $updateDebtStatusUseCase
     */
    public function __construct(
        protected UpdateDebtUseCase $updateDebtStatusUseCase
    ) {}

    /**
     * @param ExpenseDebt $debt
     * @param DebtDTO $debtDTO
     *
     * @return JsonResponse
     */
    public function update(ExpenseDebt $debt, DebtDTO $debtDTO): JsonResponse
    {
        $this->updateDebtStatusUseCase->execute($debt->id, $debtDTO);

        return response()->json(['id' => 'Debt status updated successfully'], Response::HTTP_OK);
    }
}
