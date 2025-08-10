<?php

namespace App\Events;

use App\Domain\Entity\Debt;
use App\Domain\Entity\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupDebtAmountUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Debt $debt,
        public readonly Group $group
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("group:{$this->debt->groupId}");
    }

    public function broadcastAs(): string
    {
        return 'groupDebtAmountUpdated';
    }

    /**
     * @return string[]
     */
    public function broadcastConnections(): array
    {
        return ['redis'];
    }
}
