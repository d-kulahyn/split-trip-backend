<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Payer
{
    /**
     * @param int $id
     * @param float $amount
     * @param string $currency
     * @param string|null $avatar
     */
    public function __construct(
        public int $id,
        public float $amount,
        public string $currency,
        public ?string $avatar = null
    ) {}
}
