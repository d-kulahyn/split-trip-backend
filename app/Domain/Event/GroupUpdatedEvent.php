<?php

namespace App\Domain\Event;

use App\Domain\Entity\Group;
use App\Infrastrstructure\API\Resource\GroupResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Group $group,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("group:{$this->group->id}");
    }

    public function broadcastWith(): array
    {
        return ['group' => new GroupResource($this->group)];
    }

    public function broadcastAs(): string
    {
        return 'groupUpdated';
    }

    /**
     * @return string[]
     */
    public function broadcastConnections(): array
    {
        return ['redis'];
    }
}
