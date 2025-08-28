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
            ->with([
                'createdBy',
                'group:id,name',
            ])
            ->withWhereHas('customers', function ($q) use ($status, $to) {
                $q->select('customers.id')
                    ->where('status', $status->value)
                    ->where('customers.id', $to);
            })
            ->get()
            ->map(function (\App\Models\ActivityLog $activityLog) use ($status) {
                return new ActivityLog(
                    groupId    : $activityLog->group_id,
                    groupName  : $activityLog->group->name,
                    actionType : $activityLog->action_type,
                    customerIds: $activityLog->customers->pluck('id')->all(),
                    createdBy  : CustomerEloquentToDomainEntity::toEntity($activityLog->createdBy),
                    status     : $status,
                    createdAt  : $activityLog->created_at ? $activityLog->created_at->getTimestamp() : null,
                    details    : $activityLog->details,
                    id         : $activityLog->id
                );
            });
    }
}
