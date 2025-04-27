<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\DebtStatusEnum;

class Debt
{
    /**
     * @param float $amount
     * @param string $currency
     * @param string $groupId
     * @param int $from
     * @param int $to
     * @param DebtStatusEnum $status
     * @param int|null $id
     */
    public function __construct(
        public float $amount,
        public string $currency,
        public string $groupId,
        public int $from,
        public int $to,
        public DebtStatusEnum $status = DebtStatusEnum::PENDING,
        public ?int $id = null,
    ) {}
}
