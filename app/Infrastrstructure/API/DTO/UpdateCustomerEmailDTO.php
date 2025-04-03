<?php

namespace App\Infrastrstructure\API\DTO;

use Illuminate\Validation\Rules\Unique;
use Spatie\LaravelData\Data;

class UpdateCustomerEmailDTO extends Data
{
    /**
     * @param string $email
     */
    public function __construct(
        public readonly string $email,
    ) {}

    /**
     * @return array[]
     */
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email',  (new Unique('customers', 'email'))],
        ];
    }
}
