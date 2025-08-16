<?php

namespace App\Events;

use App\Infrastrstructure\API\Resource\ActivityResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $customerId,
        public ActivityResource $activity
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("activity:{$this->customerId}");
    }

    public function broadcastAs(): string
    {
        return 'activityCreated';
    }

    /**
     * @return string[]
     */
    public function broadcastConnections(): array
    {
        return ['redis'];
    }
}
