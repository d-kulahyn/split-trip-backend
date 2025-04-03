<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Member
{
    /**
     * @param int|null $id
     */
    public function __construct(
        public ?int $id = null
    ) {}
}
