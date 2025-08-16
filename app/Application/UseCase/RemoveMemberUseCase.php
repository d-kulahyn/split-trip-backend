<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\API\Exceptions\UnauthorizedGroupActionException;

readonly class RemoveMemberUseCase
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
     * @param int $authUserId
     * @param string $groupId
     * @param array $members
     *
     * @throws UnauthorizedGroupActionException
     * @return void
     */
    public function execute(int $authUserId, string $groupId, array $members): void
    {
        $group = $this->groupReadRepository->findById($groupId);

        $isCreator = $group->createdBy === $authUserId;

        if (!in_array($authUserId, $members) && !$isCreator) {
            throw new UnauthorizedGroupActionException();
        }

        $isSelfRemove = count($members) === 1 && $members[0] === $authUserId;

        if (!$isCreator && !$isSelfRemove) {
            throw new UnauthorizedGroupActionException();
        }

        $balances = $group->getBalances($members);

        foreach ($balances as $balance) {
            if ($balance->balance != 0) {
                throw new UnauthorizedGroupActionException('It is impossible to remove a group member with unpaid debts or if they are owed money by someone else.');
            }
        }

        $currentGroupMembers = $group->getMembers();
        if (count($currentGroupMembers) === 1) {
            $this->groupWriteRepository->remove($group);

            return;
        }

        $group->removeMember($members);

        $this->groupWriteRepository->save($group);
    }
}
