<?php

declare(strict_types=1);

namespace App\Listener;

use App\Events\GroupCreatedEvent;
use Illuminate\Support\Facades\Cache;
use App\Domain\Repository\GroupReadRepositoryInterface;

readonly class GroupInvalidationCacheListener
{
    public function __construct(
        private GroupReadRepositoryInterface $groupReadRepository
    ) {}

    public function handle(GroupCreatedEvent $event): void
    {
        $group = $this->groupReadRepository->findById($event->groupId);

        foreach ($group->getMemberIds() as $memberId) {
            Cache::forget("groups:{$memberId}");
        }
    }
}
