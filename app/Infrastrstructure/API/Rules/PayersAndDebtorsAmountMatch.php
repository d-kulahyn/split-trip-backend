<?php

namespace App\Infrastrstructure\API\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PayersAndDebtorsAmountMatch implements ValidationRule
{
    public function __construct(
        protected readonly array $payers,
        protected readonly array $debtors
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $payerTotal = round(collect($this->payers)->sum('amount'), 2);
        $debtorTotal = round(collect($this->debtors)->sum('amount'), 2);

        if ($payerTotal !== $debtorTotal) {
            $fail('The total amount of payers and debtors must match.');
        }
    }
}
