<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\API\Exceptions\UserAlreadyInGroupException;

readonly class AddMemberUseCase
{
    /**
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     */
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository,
        private CustomerReadRepositoryInterface $customerReadRepository,
        private GroupWriteRepositoryInterface $groupWriteRepository,
    ) {}

    /**
     * @throws UserAlreadyInGroupException
     */
    public function execute(string $groupId, int $newMemberId): void
    {
        if ($this->groupReadRepository->isAMemberOfGroup($groupId, $newMemberId)) {
            throw new UserAlreadyInGroupException();
        }

        $newMember = $this->customerReadRepository->findById([$newMemberId])->first();

        $group = $this->groupReadRepository->findById($groupId)->addMember($newMember);

        $this->groupWriteRepository->save($group);
    }
}
