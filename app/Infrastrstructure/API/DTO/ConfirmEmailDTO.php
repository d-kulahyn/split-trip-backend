<?php

namespace App\Infrastrstructure\API\DTO;

use App\Infrastrstructure\API\Rules\ConfirmationEmailCodeRule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\LaravelData\Data;

class ConfirmEmailDTO extends Data
{
    /**
     * @param string $code
     */
    public function __construct(
        public readonly string $code
    ) {}

    /**
     * @param ...$args
     * @return array[]
     * @throws BindingResolutionException
     */
    public static function rules(...$args): array
    {
        return [
            'code' => ['required', app()->make(ConfirmationEmailCodeRule::class)]
        ];
    }
}
