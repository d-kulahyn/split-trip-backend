<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class DebtorDTO extends Data
{
    /**
     * @param int $id
     * @param float|null $amount
     */
    public function __construct(
        public readonly int $id,
        public ?float $amount = null,
    ) {}

    /**
     * @param ...$args
     *
     * @return array[]
     */
    public static function rules(...$args): array
    {
        return [
            'id'       => ['required', 'integer'],
            'amount'   => ['nullable', 'numeric']
        ];
    }
}
