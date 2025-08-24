<?php

namespace App\Domain\Event;

use App\Domain\Repository\GroupReadRepositoryInterface;
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
        public string $groupId,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("group:{$this->groupId}");
    }

    public function broadcastWith(): array
    {
        $groupRepository = app(GroupReadRepositoryInterface::class);

        return ['group' => new GroupResource($groupRepository->getById($this->groupId))];
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
