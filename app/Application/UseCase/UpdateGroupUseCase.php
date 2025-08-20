<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\RequestGroupDTO;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\Service\CurrencyConverterService;

readonly class UpdateGroupUseCase
{
    /**
     * @param CurrencyConverterService $currencyConverterService
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     * @param GroupReadRepositoryInterface $groupReadRepository
     */
    public function __construct(
        public CurrencyConverterService $currencyConverterService,
        public GroupWriteRepositoryInterface $groupWriteRepository,
        public GroupReadRepositoryInterface $groupReadRepository
    ) {}

    /**
     * @param string $groupId
     * @param RequestGroupDTO $groupDTO
     *
     * @return void
     */
    public function execute(string $groupId, RequestGroupDTO $groupDTO): void
    {
        $group = $this->groupReadRepository->findById($groupId);

        /** Recalculate all debts */
//        if ($group->finalCurrency !== $groupDTO->currency) {
//            foreach ($group->getDebts() as $debt) {
//                if ($debt->status === StatusEnum::PAID) continue;
//
//                $debt->amount = $this->currencyConverterService->convert(
//                    $debt->currency,
//                    $debt->amount,
//                    $groupDTO->currency
//                );
//
//                $debt->currency = $groupDTO->currency;
//            }
//        }

        $group->name = $groupDTO->name;
        $group->category = $groupDTO->category;
//        $group->finalCurrency = $groupDTO->currency;
        $group->simplifyDebts = $groupDTO->simplify_debts ?? true;

        $this->groupWriteRepository->save($group);
    }
}
