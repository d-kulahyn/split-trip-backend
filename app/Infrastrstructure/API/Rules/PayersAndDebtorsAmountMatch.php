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
        $payerTotal = collect($this->payers)->sum('amount');
        $debtorTotal = collect($this->debtors)->sum('amount');
        
        header('Content-Type: application/json');
        echo json_encode([$payerTotal, $debtorTotal]);die;

        if ($payerTotal !== $debtorTotal) {
            $fail('The total amount of payers and debtors must match.');
        }
    }
}
