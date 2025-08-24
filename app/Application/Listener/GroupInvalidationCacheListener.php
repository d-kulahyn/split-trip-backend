<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Event\GroupDeletedEvent;
use App\Domain\Event\GroupUpdatedEvent;
use App\Domain\Repository\GroupReadRepositoryInterface;
use Illuminate\Support\Facades\Cache;

readonly class GroupInvalidationCacheListener
{
    public function __construct(
        public readonly GroupReadRepositoryInterface $groupReadRepository,
    ) {}

    public function handle(GroupUpdatedEvent|GroupDeletedEvent $event): void
    {
        $group = $this->groupReadRepository->findById($event->groupId);

        Cache::forget("group:{$group->id}:balances");

        foreach ($group->getMemberIds() as $customerId) {
            Cache::forget("customer:{$customerId}:groups");
            Cache::forget("customer:{$customerId}:balance");
        }
    }
}
