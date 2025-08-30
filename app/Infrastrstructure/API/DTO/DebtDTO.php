<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class DebtDTO extends Data
{
    /**
     * @param float $amount
     * @param string $currency
     */
    public function __construct(
        public float $amount,
        public string $currency
    ) {}

    /**
     * @param ...$args
     *
     * @return string[]
     */
    public static function rules(...$args): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'currency' => 'required|string'
        ];
    }
}
