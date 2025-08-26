<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\ActivityLog;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\ActivityReadRepositoryInterface;
use App\Infrastrstructure\Mapper\CustomerEloquentToDomainEntity;
use Illuminate\Support\Collection;

class EloquentActivityLogReadRepository implements ActivityReadRepositoryInterface
{

    public function list(StatusEnum $status, int $to): Collection
    {
        return \App\Models\ActivityLog::query()
            ->with(['members' => function ($query) use ($status){
                $query->where('status', $status->value);
            }])
            ->get()
            ->map(function (\App\Models\ActivityLog $activityLog) {
                return new ActivityLog(
                    groupId   : $activityLog->group_id,
                    groupName : $activityLog->group->name,
                    actionType: $activityLog->action_type,
                    customer  : $activityLog->relationLoaded('customer')
                        ? CustomerEloquentToDomainEntity::toEntity($activityLog->customer)
                        : null,
                    createdAt : $activityLog->created_at ? $activityLog->created_at->getTimestamp() : null,
                    details   : $activityLog->details,
                    id        : $activityLog->id
                );
            });
    }
}
