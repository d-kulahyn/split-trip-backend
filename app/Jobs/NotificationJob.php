<?php

namespace App\Jobs;

use ArrayObject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly array $channels,
        public readonly ArrayObject $payload,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->channels as $channel) {
            app($channel)->send($this->payload);
        }
    }
}
