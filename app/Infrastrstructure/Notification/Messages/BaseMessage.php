<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification\Messages;

class BaseMessage extends \ArrayObject
{

    public function __get(string $key)
    {
        if (!$this->offsetExists($key)) {
            throw new \InvalidArgumentException("Property {$key} does not exist in " . self::class);
        }

        return $this->offsetGet($key);
    }
}
