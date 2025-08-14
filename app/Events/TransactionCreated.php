<?php

namespace App\Events;

use App\Domain\Entity\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public string $groupId
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("group:{$this->groupId}");
    }

    public function broadcastAs(): string
    {
        return 'transactionCreated';
    }

    /**
     * @return string[]
     */
    public function broadcastConnections(): array
    {
        return ['redis'];
    }
}
