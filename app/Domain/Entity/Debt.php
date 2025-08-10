<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\StatusEnum;

class Debt
{
    /**
     * @param float $amount
     * @param string $currency
     * @param string $groupId
     * @param Customer $from
     * @param Customer $to
     * @param StatusEnum $status
     * @param int|null $id
     */
    public function __construct(
        public float $amount,
        public string $currency,
        public string $groupId,
        public Customer $from,
        public Customer $to,
        public StatusEnum $status = StatusEnum::PENDING,
        public ?int $id = null,
    ) {}

    public function subAmount(float $amount): void
    {
        $this->amount = (float)bcsub((string)$this->amount, (string)$amount, 2);
    }
}
