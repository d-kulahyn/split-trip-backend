<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Exceptions;

use Exception;

class UserAlreadyInGroupException extends Exception
{
    /**
     * @param $message
     */
    public function __construct($message = "User is already a member of the group.")
    {
        parent::__construct($message);
    }
}
