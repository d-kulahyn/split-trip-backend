<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Infrastrstructure\Mapper\GroupEloquentToDomainEntity;

class EloquentGroupReadRepository implements GroupReadRepositoryInterface
{

    /**
     * @param string $groupId
     * @param int $userId
     *
     * @return bool
     */
    public function isAMemberOfGroup(string $groupId, int $userId): bool
    {
        $group = Group::find($groupId);

        return $group->members->contains($userId) || $group->created_by === $userId;
    }

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return Group::query();
    }

    /**
     * @param string $id
     *
     * @return \App\Domain\Entity\Group|null
     */
    public function findById(string $id): ?\App\Domain\Entity\Group
    {
        $groupEloquent = Group::with([
            'expenses' => fn ($query) => $query->orderBy('created_at', 'desc'),
            'members'
        ])->find($id);

        if (!$groupEloquent) {

            return null;
        }

        return GroupEloquentToDomainEntity::toEntity($groupEloquent);
    }

    /**
     * @param int $customerId
     *
     * @return array
     */
    public function list(int $customerId): array
    {
        $paginator = Group::query()
            ->with([
                'expenses' => fn ($query) => $query->orderBy('created_at', 'desc'),
                'members',
                'debts'
            ])
            ->where(function ($query) use ($customerId) {
                $query->where('created_by', $customerId)
                ->orWhereHas('members', function ($query) use ($customerId) {
                    $query->where('customer_id', $customerId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->simplePaginate();


        return array_map(function ($group) {
            return GroupEloquentToDomainEntity::toEntity($group);
        }, $paginator->items());
    }

    /**
     * @param string $groupId
     *
     * @return array
     */
    public function members(string $groupId): array
    {
        $group = Group::find($groupId);

        return $group->members->toArray();
    }
}
