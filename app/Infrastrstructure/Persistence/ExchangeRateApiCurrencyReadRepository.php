<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Persistence;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Domain\Repository\CurrencyReadRepositoryInterface;

class ExchangeRateApiCurrencyReadRepository implements CurrencyReadRepositoryInterface
{
    /**
     * @param string $base
     *
     * @return array
     */
    public function rates(string $base): array
    {
        return Cache::remember("exchange_rates_{$base}", 3600, function () use ($base) {
            $response = Http::get("https://v6.exchangerate-api.com/v6/367e15268baf3c1e498c0df2/latest/{$base}");

            return $response->json()['conversion_rates'] ?? [];
        });
    }

    public function codes(): array
    {
        return Cache::remember("currencies", 3600, function () {
            $response = Http::get("https://v6.exchangerate-api.com/v6/367e15268baf3c1e498c0df2/codes");

            return $response->json()['supported_codes'] ?? [];
        });
    }
}
