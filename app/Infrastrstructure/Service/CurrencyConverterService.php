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
     *
     * @return float
     */
    public function convert(string $fromCurrency, float $amount, string $toCurrency): float
    {
        if (empty($this->rates[$toCurrency])) {
            $this->rates[$toCurrency] = $this->currencyReadRepository->rates($toCurrency);
        }

        return (float)bcdiv((string)$amount, (string)$this->rates[$toCurrency][$fromCurrency]);
    }
}
