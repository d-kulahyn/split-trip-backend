<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;

readonly class ToggleSimplifyDebtsUseCase
{
    /**
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     */
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository,
        private GroupWriteRepositoryInterface $groupWriteRepository
    ) {}

    /**
     * @param string $groupId
     *
     * @return void
     */
    public function execute(string $groupId): void
    {
        $group = $this->groupReadRepository->findById($groupId);

        $group->simplifyDebts = !$group->simplifyDebts;

        $this->groupWriteRepository->save($group);
    }
}
