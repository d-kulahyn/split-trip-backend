<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Debtor
{
    /**
     * @param float $amount
     * @param int $debtorId
     * @param string $currency
     * @param int|null $id
     * @param string|null $name
     * @param string|null $avatar
     */
    public function __construct(
        public float $amount,
        public int $debtorId,
        public string $currency,
        public ?int $id = null,
        public ?string $name = null,
        public ?string $avatar = null
    ) {}
}
