<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Group;
use App\Infrastrstructure\API\DTO\RequestGroupDTO;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\Mapper\CreateGroupDTOToDomainEntity;

readonly class CreateGroupUseCase
{
    /**
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     */
    public function __construct(
        public GroupWriteRepositoryInterface $groupWriteRepository
    ) {}

    /**
     * @param RequestGroupDTO $groupDTO
     *
     * @return Group
     */
    public function execute(RequestGroupDTO $groupDTO): Group
    {
        $group = CreateGroupDTOToDomainEntity::toEntity($groupDTO);

        $this->groupWriteRepository->save($group);

        return $group;
    }
}
