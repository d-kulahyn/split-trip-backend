<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Exceptions;

use Exception;

class UnauthorizedGroupActionException extends Exception
{
    public function __construct(string $message = 'You are not allowed to remove members.')
    {
        parent::__construct($message);
    }
}
