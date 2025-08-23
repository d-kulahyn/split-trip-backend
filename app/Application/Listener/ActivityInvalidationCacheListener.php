<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Events\ActivityChangeEvent;
use Illuminate\Support\Facades\Cache;

readonly class ActivityInvalidationCacheListener
{
    public function handle(ActivityChangeEvent $event): void
    {
        Cache::forget("activity:{$event->customerId}");
    }
}
