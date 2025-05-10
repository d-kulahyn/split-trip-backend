<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Payer
{
    /**
     * @param float $amount
     * @param string $currency
     * @param int $payerId
     * @param int|null $id
     * @param string|null $avatarColor
     * @param string|null $name
     */
    public function __construct(
        public float $amount,
        public string $currency,
        public int $payerId,
        public ?int $id = null,
        public ?string $avatarColor = null,
        public ?string $name = null,
    ) {}
}
