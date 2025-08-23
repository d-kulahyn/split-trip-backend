<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Infrastrstructure\Mapper\GroupEloquentToDomainEntity;
use Illuminate\Support\Facades\Cache;

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
     * @param array|null $with
     *
     * @return \App\Domain\Entity\Group|null
     */
    public function findById(string $id, ?array $with = null): ?\App\Domain\Entity\Group
    {
        if ($with === null) {
            $with = [
                'expenses' => fn ($query) => $query->orderBy('created_at', 'desc'),
                'members',
                'debts',
            ];
        }

        $groupEloquent = Group::with($with)->find($id);

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
        return Cache::remember("groups:{$customerId}", 3600, function () use ($customerId) {
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
        } );
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

    /**
     * @param string $groupId
     * @param int $customerId
     *
     * @return bool
     */
    public function isAMember(string $groupId, int $customerId): bool
    {
        return Group::find()->members()->where('customer_id', $customerId)->exists();
    }

    /**
     * @param string $id
     *
     * @return string|null
     */
    public function getNameById(string $id): ?string
    {
        return Group::find($id)?->name;
    }
}
