<?php

namespace App\Domain\Event;

use App\Domain\Entity\Group;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Group $group,
    ) {}
}
