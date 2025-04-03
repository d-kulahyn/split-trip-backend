<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Pay
{
    /**
     * @param float $amount
     * @param string $currency
     * @param int $payerId
     * @param int|null $id
     */
    public function __construct(
        public float $amount,
        public string $currency,
        public int $payerId,
        public ?int $id = null
    ) {}
}
