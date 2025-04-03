<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Debtor
{
    /**
     * @param int $id
     * @param float $amount
     */
    public function __construct(
        public int $id,
        public float $amount,
    ) {}
}
