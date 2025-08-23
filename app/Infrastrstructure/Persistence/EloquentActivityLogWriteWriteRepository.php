<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\ActivityLog;
use App\Domain\Enum\StatusEnum;
use App\Domain\Events\ActivityChangeEvent;
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
            'status'      => $activity->status->value,
        ]);

        $eloquentActivityLog->save();

        $activity->id = $eloquentActivityLog->id;
        $activity->createdAt = $eloquentActivityLog->created_at->getTimestamp();

        ActivityChangeEvent::dispatch($activity->customerId);

        return $activity;
    }

    public function updateStatuses(array $ids, StatusEnum $status): void
    {
        \App\Models\ActivityLog::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status->value]);

        ActivityChangeEvent::dispatch(auth()->id());
    }
}
