<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Events\GroupCreatedEvent;
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
