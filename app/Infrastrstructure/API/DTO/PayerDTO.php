<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class PayerDTO extends Data
{
    /**
     * @param int $id
     * @param string $currency
     * @param float|null $amount
     */
    public function __construct(
        public readonly int $id,
        public readonly string $currency,
        public readonly ?float $amount = null,
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
            'amount'   => ['nullable', 'numeric'],
            'currency' => 'required|string|max:3',
        ];
    }
}
