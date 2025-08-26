<?php

namespace App\Infrastrstructure\API\DTO;

use Spatie\LaravelData\Data;

class CreateUserDTO extends Data
{
    public function __construct(
        public string $password,
        public string $email,
        public string $avatar_color,
        public string $currency = 'USD',
        public ?string $name = null,
        public ?string $firebase_cloud_messaging_token = null,
    ) {}

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'email'            => ['required', 'email', 'unique:customers,email'],
            'password'         => ['required', 'min:8'],
            'avatar_color'     => ['required'],
            'confirm_password' => ['required', 'same:password'],
        ];
    }
}
