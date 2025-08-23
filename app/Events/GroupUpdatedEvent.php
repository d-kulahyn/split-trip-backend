<?php

namespace App\Events;

use App\Domain\Repository\GroupReadRepositoryInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupUpdatedEvent
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
        $repository = app(GroupReadRepositoryInterface::class);

        return ['group' => $repository->findById($this->groupId)];
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
