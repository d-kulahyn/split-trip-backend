<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Spatie\LaravelData\Data;

class Transaction extends Data
{

    public function __construct(
        public int $from,
        public int $to,
        public float $amount,
        public string $currency,
        public string $groupId,
        public ?int $id = null,
    ) {}
}
