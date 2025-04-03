<?php

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class UpdateCustomerPasswordDTO extends Data
{
    /**
     * @param string $new_password
     */
    public function __construct(
        public readonly string $new_password,
    ) {}

    /**
     * @return array[]
     */
    public static function rules(): array
    {
        return [
            'old_password'     => ['required', 'string', 'current_password'],
            'new_password'     => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'same:new_password'],
        ];
    }
}
