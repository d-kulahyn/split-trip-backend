<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\StatusEnum;
use Spatie\LaravelData\Data;

class Transaction extends Data
{

    public function __construct(
        public Customer $from,
        public Customer $to,
        public float $amount,
        public string $currency,
        public string $groupId,
        public string $groupName,
        public float $rate,
        public ?int $id = null,
        public ?Group $group = null,
        public StatusEnum $status = StatusEnum::PENDING
    ) {}
}
