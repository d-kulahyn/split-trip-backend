<?php

namespace App\Domain\Repository;

use App\Domain\Entity\ActivityLog;

interface ActivityWriteRepositoryInterface
{
    public function save(ActivityLog $activity): ActivityLog;
}
