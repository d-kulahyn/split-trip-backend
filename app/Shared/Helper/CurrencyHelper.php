<?php

declare(strict_types=1);

namespace App\Shared\Helper;

class CurrencyHelper
{

    public static function currency_symbol(float $value, string $code, ?string $locale = null): ?string
    {
        $locale ??= \Locale::getDefault();

        $fmt = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $s = $fmt->formatCurrency($value, $code);
        if ($s === false) {

            return null;
        }

        return $s;
    }
}
