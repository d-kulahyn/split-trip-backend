<?php

namespace App\Shared\Trait;

use UnitEnum;

trait AllCasesTrait
{
    /**
     * @param UnitEnum[] $unitEnums
     * @return array
     */
    public static function getCases(?array $unitEnums = null): array
    {
        $result = [];
        $enums = $unitEnums ?: self::cases();
        foreach ($enums as $index => $case) {
            $result[$index] = [
                'label' => $case->name,
                'value' => $case->value
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
