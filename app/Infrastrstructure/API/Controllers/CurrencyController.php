<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Domain\Repository\CurrencyReadRepositoryInterface;

readonly class CurrencyController
{
    /**
     * @param CurrencyReadRepositoryInterface $currencyReadRepository
     */
    public function __construct(
        public CurrencyReadRepositoryInterface $currencyReadRepository
    ) {}

    /**
     * @param string $currency
     *
     * @return array
     */
    public function rates(string $currency): array
    {
        return $this->currencyReadRepository->rates($currency);
    }

    /**
     * @return array
     */
    public function codes(): array
    {
        return $this->currencyReadRepository->codes();
    }
}
