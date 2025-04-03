<?php

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class LoginDTO extends Data
{
    public function __construct(
        public string $password,
        public string $email
    ) {}

    public static function rules(...$args): array
    {
        return [
            'password' => ['required', 'min:8'],
            'email'    => ['email', 'required'],
        ];
    }
}
