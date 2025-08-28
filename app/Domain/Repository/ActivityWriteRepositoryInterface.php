<?php

namespace App\Domain\Repository;

use App\Domain\Entity\ActivityLog;
use App\Domain\Enum\StatusEnum;

interface ActivityWriteRepositoryInterface
{
    public function save(ActivityLog $activity): ActivityLog;
    public function updateStatuses(array $ids, int $customerId, StatusEnum $status): void;
}
