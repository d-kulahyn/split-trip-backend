<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use Spatie\LaravelData\Data;

class CreateGroupDto extends Data
{

    public function __construct(
        public string $name,
        public string $currency,
        public bool $simplifyDebts = true,
        public string $category = 'Other',
        public array $members = []
    ) {}
}
