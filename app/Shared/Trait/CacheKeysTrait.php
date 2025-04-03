<?php

namespace App\Shared\Trait;

trait CacheKeysTrait
{
    /**
     * @param array $params
     * @return string
     */
    public function map(array $params = []): string
    {
        $key = '';
        if (!empty($params)) {
            $key = str_replace(array_keys($params), array_values($params), $this->value);
        }

        return $key;
    }
}
