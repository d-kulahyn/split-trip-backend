<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class RequestGroupDTO extends Data
{
    /**
     * @param string $name
     * @param string $category
     * @param int|null $created_by
     * @param string $currency
     */
    public function __construct(
        public string $name,
        public string $category,
        public ?int $created_by = null,
        public string $currency = 'USD',
    ) {}

    /**
     * @param ...$args
     *
     * @return array[]
     */
    public static function rules(...$args): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:3'],
        ];
    }
}
