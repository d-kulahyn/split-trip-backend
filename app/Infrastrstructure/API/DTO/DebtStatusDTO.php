<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;
use App\Domain\Enum\DebtStatusEnum;

class DebtStatusDTO extends Data
{
    /**
     * @param DebtStatusEnum $status
     */
    public function __construct(
        public DebtStatusEnum $status,
    ) {}

    /**
     * @param ...$args
     *
     * @return string[]
     */
    public static function rules(...$args): array
    {
        return [
            'status' => 'required|in:'.implode(',', DebtStatusEnum::values())
        ];
    }
}
