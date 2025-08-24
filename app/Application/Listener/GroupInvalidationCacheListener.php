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
        foreach ($event->group->getMemberIds() as $uid) {
            Cache::tags(["customer:{$uid}", 'groups'])->flush();
        }
    }
}
