<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Mapper;

use App\Domain\Entity\Expense;
use App\Domain\Entity\Group;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\DebtWriteRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use App\Infrastrstructure\API\DTO\RequestGroupDTO;

class CreateGroupDTOToDomainEntity
{

    /**
     * @param RequestGroupDTO $groupDTO
     * @param string|null $id
     *
     * @return Group
     */
    public static function toEntity(RequestGroupDTO $groupDTO, ?string $id = null): Group
    {
        $domainGroup = new Group(
            $groupDTO->name,
            $groupDTO->category,
            $groupDTO->created_by,
            $groupDTO->currency,
            app(DebtWriteRepositoryInterface::class),
            app(TransactionWriteRepositoryInterface::class),
            true,
            $id
        );

        if (!empty($groupDTO->members)) {
            $customersReadRepository = app(CustomerReadRepositoryInterface::class);

            $customers = $customersReadRepository->findById($groupDTO->members);

            $domainGroup->setMembers($customers);
        }

        foreach ($groupDTO->expenses as $expense) {
            $domainGroup->addExpense(new Expense(
                $expense->category,
                $expense->createdAt,
                $expense->finalCurrency,
                $expense->description,
                $expense->id
            ));
        }

        return $domainGroup;
    }
}
