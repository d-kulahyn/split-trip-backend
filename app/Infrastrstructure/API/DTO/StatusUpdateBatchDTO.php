<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use App\Domain\Enum\StatusEnum;
use Spatie\LaravelData\Data;

class StatusUpdateBatchDTO extends Data
{

    public function __construct(
        public array $ids,
        public StatusEnum $status
    ) {}

    public static function rules(...$args): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:activity_logs,id',
            'status' => 'required|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
