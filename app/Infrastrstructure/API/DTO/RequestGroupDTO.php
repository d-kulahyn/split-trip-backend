<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class RequestGroupDTO extends Data
{
    /**
     * @param string $name
     * @param string $category
     * @param int|null $created_by
     * @param array $members
     * @param array $expenses
     * @param bool|Optional $simplify_debts
     * @param string $currency
     */
    public function __construct(
        public string $name,
        public string $category,
        public ?int $created_by = null,
        public array $members = [],
        public array $expenses = [],
        public bool $simplify_debts = true,
        public string $currency = 'EUR',
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
