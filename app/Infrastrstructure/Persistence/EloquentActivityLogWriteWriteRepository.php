<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Enum\StatusEnum;
use App\Domain\Entity\ActivityLog;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Models\ActivityLogCustomer;

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
            'created_by'  => $activity->createdBy->id,
            'group_id'    => $activity->groupId,
            'action_type' => $activity->actionType->value,
            'details'     => $activity->details,
        ]);

        $eloquentActivityLog->save();

        $eloquentActivityLog->customers()->sync($activity->customerIds);

        $eloquentActivityLog->customers()->updateExistingPivot(
            $activity->createdBy->id,
            ['status' => StatusEnum::READ->value]
        );

        $activity->id = $eloquentActivityLog->id;
        $activity->createdAt = $eloquentActivityLog->created_at->getTimestamp();

        return $activity;
    }

    public function updateStatuses(array $ids, int $customerId, StatusEnum $status): void
    {
        ActivityLogCustomer::query()
            ->whereIn('activity_log_id', $ids)
            ->where('customer_id', $customerId)
            ->update(['status' => $status->value]);
    }
}
