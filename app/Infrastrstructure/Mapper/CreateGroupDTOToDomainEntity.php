<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Mapper;

use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\RequestGroupDTO;

class CreateGroupDTOToDomainEntity
{

    /**
     * @param RequestGroupDTO $groupDTO
     * @param string|null $id
     *
     * @return \App\Domain\Entity\Group
     */
    public static function toEntity(RequestGroupDTO $groupDTO, ?string $id = null): \App\Domain\Entity\Group
    {
        $domainGroup = new \App\Domain\Entity\Group(
            $groupDTO->name,
            $groupDTO->category,
            $groupDTO->created_by,
            $groupDTO->currency,
            true,
            $id
        );

        if (!empty($groupDTO->members)) {
            $customersReadRepository = app(CustomerReadRepositoryInterface::class);

            $customers = $customersReadRepository->findById($groupDTO->members);

            $domainGroup->setMembers($customers);
        }

        foreach ($groupDTO->expenses as $expense) {
            $domainGroup->addExpense(new \App\Domain\Entity\Expense(
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
