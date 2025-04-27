<?php

declare(strict_types=1);

namespace App\Application;

class DebtException extends \Exception
{

    public function __construct(string $message = "Debt not found", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
