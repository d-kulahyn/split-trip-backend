<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Service;

use App\Domain\Repository\CurrencyReadRepositoryInterface;

class CurrencyConverterService
{
    public array $rates = [];

    /**
     * @param CurrencyReadRepositoryInterface $currencyReadRepository
     */
    public function __construct(
        public readonly CurrencyReadRepositoryInterface $currencyReadRepository
    ) {}

    /**
     * @param string $fromCurrency
     * @param float $amount
     * @param string $toCurrency
     * @param float|null $rate
     *
     * @return float
     */
    public function convert(string $fromCurrency, float $amount, string $toCurrency, ?float $rate = null): float
    {
        $this->rates[$toCurrency][$fromCurrency] = $rate;

        if (empty($this->rates[$toCurrency]) || $rate === null) {
            $this->rates[$toCurrency] = $this->currencyReadRepository->rates($toCurrency);
        }

        return (float)bcdiv((string)$amount, (string)$this->rates[$toCurrency][$fromCurrency]);
    }
}
