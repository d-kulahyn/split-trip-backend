<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entity\Debt;
use App\Domain\ValueObject\Balance;
use Illuminate\Support\Collection;

class DebtDistributor
{
    /**
     * @param Collection $balances
     * @param string $currency
     * @param string $groupId
     *
     * @return array
     */
    public function distributeDebts(Collection $balances, string $currency, string $groupId): array
    {
        $debts = [];

        $debtors = $balances->filter(fn(Balance $b) => $b->balance < 0)->sortBy('balance');
        $payers = $balances->filter(fn(Balance $b) => $b->balance > 0)->sortByDesc('balance');

        /** @var array<Balance> $debtors */
        foreach ($debtors as $debtorId => $debtor) {
            /** @var array<Balance> $payers */
            foreach ($payers as $payerId => $payer) {

                if ($debtor->balance == 0) {
                    break;
                }

                if ($payer->balance == 0) {
                    continue;
                }

                $amount = min(abs($debtor->balance), $payer->balance);

                $debtor->balance = (float)bcadd((string)$debtor->balance, (string)$amount);
                $payer->balance  = (float)bcsub((string)$payer->balance, (string)$amount);

                $debts[] = new Debt(
                    $amount,
                    $currency,
                    $groupId,
                    $debtorId,
                    $payerId,
                );
            }
        }

        return $debts;
    }
}
