<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\ActivityLog;
use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Repository\ActivityWriteRepositoryInterface;

readonly class LogActivityUseCase
{

    /**
     * @param ActivityWriteRepositoryInterface $repository
     */
    public function __construct(private ActivityWriteRepositoryInterface $repository) {}

    /**
     * @param int $customerId
     * @param string $groupId
     * @param ActivityLogActionTypeEnum $actionType
     * @param array $details
     *
     * @return void
     */
    public function execute(int $customerId, string $groupId, ActivityLogActionTypeEnum $actionType, array $details = []): void
    {
        $activity = new ActivityLog($customerId, $groupId, $actionType, $details);

        $this->repository->save($activity);
    }
}
