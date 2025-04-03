<?php

namespace App\Domain\Repository;

interface CurrencyReadRepositoryInterface
{
    public function rates(string $base): array;
    public function codes(): array;
}
