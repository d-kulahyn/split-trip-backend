<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Dto\CreateGroupDto;
use App\Domain\Entity\Group;
use App\Domain\Repository\DebtWriteRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;

class GroupFactory
{

    public function create(CreateGroupDto $createGroupDto, int $customerId): Group
    {
        return new Group(
            $createGroupDto->name,
            $createGroupDto->category,
            $customerId,
            $createGroupDto->currency,
            app(DebtWriteRepositoryInterface::class),
            app(TransactionWriteRepositoryInterface::class),
            $createGroupDto->simplifyDebts,
            members: $createGroupDto->members,
        );
    }
}
