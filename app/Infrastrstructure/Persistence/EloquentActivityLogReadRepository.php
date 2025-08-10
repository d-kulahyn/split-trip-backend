<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Domain\Entity\ActivityLog;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\ActivityReadRepositoryInterface;
use App\Infrastrstructure\Mapper\CustomerEloquentToDomainEntity;
use App\Infrastrstructure\Persistence\Mappers\EloquentGroupMapper;
use Illuminate\Support\Collection;

class EloquentActivityLogReadRepository implements ActivityReadRepositoryInterface
{

    public function list(StatusEnum $status, int $to, array $with = []): Collection
    {
        return \App\Models\ActivityLog::query()
            ->where('status', $status->value)
            ->where('customer_id', $to)
            ->with($with)
            ->get()
            ->map(function (\App\Models\ActivityLog $activityLog) {
                return new ActivityLog(
                    customerId: $activityLog->customer_id,
                    groupId   : $activityLog->group_id,
                    actionType: $activityLog->action_type,
                    customer  : $activityLog->relationLoaded('customer')
                        ? CustomerEloquentToDomainEntity::toEntity($activityLog->customer)
                        : null,
                    createdAt : $activityLog->created_at ? $activityLog->created_at->getTimestamp() : null,
                    status    : $activityLog->status,
                    details   : $activityLog->details,
                    group     : $activityLog->relationLoaded('group') ? EloquentGroupMapper::map($activityLog->group) : null,
                    id        : $activityLog->id
                );
            });
    }
}
