<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Balance;
use App\Domain\Services\DebtDistributor;
use App\Infrastrstructure\Service\CurrencyConverterService;

class Expense
{
    /**
     * @param string $category
     * @param int $createdAt
     * @param string $currency
     * @param string $description
     * @param string $groupId
     * @param int|null $id
     * @param array $debts
     * @param array $debtors
     * @param array $payers
     */
    public function __construct(
        public readonly string $category,
        public readonly int $createdAt,
        public readonly string $currency,
        public readonly string $description,
        public readonly string $groupId,
        public ?int $id = null,
        public array $debts = [],
        public array $debtors = [],
        public array $payers = []
    ) {}

    /**
     * @param Payer $payer
     *
     * @return void
     */
    public function addPayer(Payer $payer): void
    {
        $this->payers[$payer->payerId] = $payer;
    }

    /**
     * @param Debtor $debtor
     *
     * @return void
     */
    public function addDebtor(Debtor $debtor): void
    {
        $this->debtors[$debtor->debtorId] = $debtor;
    }

    /**
     * @param CurrencyConverterService $currencyConverterService
     * @param string $groupCurrency
     *
     * @return array
     */
    public function distributeDebts(
        CurrencyConverterService $currencyConverterService,
        string $groupCurrency
    ): array {
        $this->normalizePayerAmounts($currencyConverterService, $groupCurrency);
        $balances = collect($this->getBalances());

        /** @var DebtDistributor $debtsDistributor */
        $debtsDistributor = app(DebtDistributor::class);

        $this->debts = $debtsDistributor->distributeDebts($balances, $this->currency, $this->groupId);

        return $this->debts;
    }

    /**
     * @param CurrencyConverterService $currencyConverterService
     * @param string $groupCurrency
     *
     * @return void
     */
    protected function normalizePayerAmounts(
        CurrencyConverterService $currencyConverterService,
        string $groupCurrency
    ): void {
        foreach ($this->payers as $payer) {
            if ($payer->currency !== $groupCurrency) {
                $payer->amount = $currencyConverterService->convert(
                    $payer->currency,
                    $payer->amount,
                    $groupCurrency
                );
                $payer->currency = $groupCurrency;
            }
        }
        if ($this->currency !== $groupCurrency) {
            foreach ($this->debtors as $debtor) {
                $debtor->amount = $currencyConverterService->convert(
                    $this->currency,
                    $debtor->amount,
                    $groupCurrency
                );
            }
        }
    }

    /**
     * @return array
     */
    public function getBalances(): array
    {
        $balances = [];

        foreach ($this->debtors as $debtor) {
            $balances[$debtor->debtorId] = new Balance(
                owe    : $debtor->amount,
                paid   : 0,
                balance: 0
            );
        }

        foreach ($this->payers as $payer) {
            if (!isset($balances[$payer->payerId])) {
                $balances[$payer->payerId] = new Balance(
                    owe    : 0,
                    paid   : $payer->amount,
                    balance: 0
                );
            } else {
                $balances[$payer->payerId]->paid = $payer->amount;
            }
        }

        foreach ($balances as $balance) {
            $balance->balance = (float)bcsub((string)$balance->paid, (string)$balance->owe);
        }

        return $balances;
    }

    /**
     * @return array<Debt>
     */
    public function getDebts(): array
    {
        return $this->debts;
    }

    /**
     * @return Payer[]
     */
    public function getPayers(): array
    {
        return $this->payers;
    }

    /**
     * @return Debtor[]
     */
    public function getDebtors(): array
    {
        return $this->debtors;
    }

    /**
     * @return float
     */
    public function credits(): float
    {
        $amount = 0;
        foreach ($this->payers as $pay) {
            $amount = (float)bcadd((string)$amount, (string)$pay->amount);
        }

        return $amount;
    }

    public function owe(int $customerId): float
    {
        $owed = 0.0;

        foreach ($this->debtors as $debtor) {
            if ($debtor->debtorId === $customerId) {
                $owed = (float)bcadd((string)$owed, (string)$debtor->amount);
            }
        }

        return $owed;
    }

    public function paid(int $customerId): float
    {
        $paid = 0.0;

        foreach ($this->payers as $payer) {
            if ($payer->payerId === $customerId) {
                $paid = (float)bcadd((string)$paid, (string)$payer->amount);
            }
        }

        return $paid;
    }
}
