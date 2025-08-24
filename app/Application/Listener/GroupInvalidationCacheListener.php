<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Event\GroupDeletedEvent;
use App\Domain\Event\GroupUpdatedEvent;
use Illuminate\Support\Facades\Cache;

readonly class GroupInvalidationCacheListener
{
    public function handle(GroupUpdatedEvent|GroupDeletedEvent $event): void
    {
        Cache::forget("group:{$event->group->id}:balances");

        foreach ($event->group->getMemberIds() as $customerId) {
            Cache::forget("customer:{$customerId}:groups");
            Cache::forget("customer:{$customerId}:balance");
        }
    }
}
