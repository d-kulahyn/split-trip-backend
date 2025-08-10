<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use App\Domain\Enum\StatusEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdateTransactionStatusDTO extends Data
{

    public function __construct(
        public readonly StatusEnum $status,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'status' => ['required', 'string', 'in:'.implode(',', StatusEnum::values())],
        ];
    }
}
