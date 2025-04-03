<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Service;

use Illuminate\Support\Facades\Hash;

class PasswordEncoder
{
    /**
     * @param string $password
     *
     * @return string
     */
    public function hash(string $password): string
    {
        return Hash::make($password);
    }
}
