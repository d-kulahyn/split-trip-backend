<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\ActivityLog;
use App\Domain\Repository\ActivityWriteRepositoryInterface;

class EloquentActivityLogWriteWriteRepository implements ActivityWriteRepositoryInterface
{
    /**
     * @param ActivityLog $activity
     *
     * @return ActivityLog
     */
    public function save(ActivityLog $activity): ActivityLog
    {
        $eloquentActivityLog = \App\Models\ActivityLog::find($activity->id);

        if (!$eloquentActivityLog) {
            $eloquentActivityLog = new \App\Models\ActivityLog();
        }

        $eloquentActivityLog->fill([
            'customer_id' => $activity->customerId,
            'group_id'    => $activity->groupId,
            'action_type' => $activity->actionType->value,
            'details'     => $activity->details,
        ]);

        $eloquentActivityLog->save();

        $activity->id = $eloquentActivityLog->id;

        return $activity;
    }
}
