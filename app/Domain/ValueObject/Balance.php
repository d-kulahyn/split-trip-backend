<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Spatie\LaravelData\Data;

class Balance extends Data
{
    /**
     * @param float $owe
     * @param float $paid
     * @param float $balance
     * @param int|null $customerId
     */
    public function __construct(
        public float $owe = 0.0,
        public float $paid = 0.0,
        public float $balance = 0.0,
        public ?int $customerId = null
    ) {}
}
