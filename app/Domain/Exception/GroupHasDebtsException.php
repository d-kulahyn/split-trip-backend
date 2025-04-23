<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class GroupHasDebtsException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Group has debts, cannot be deleted');
    }
}
