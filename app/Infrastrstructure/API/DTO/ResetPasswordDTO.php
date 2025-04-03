<?php

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class ResetPasswordDTO extends Data
{
    public function __construct(
        public readonly string $email
    ) {}


    public static function rules(...$args): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email']
        ];
    }
}
