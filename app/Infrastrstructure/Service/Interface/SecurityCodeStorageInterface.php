<?php

namespace App\Infrastrstructure\Service\Interface;

interface SecurityCodeStorageInterface
{
    /**
     * @param int $userId
     * @return string
     */
    public function set(int $userId): string;

    /**
     * @param int $userId
     * @return string|null
     */
    public function extract(int $userId): ?string;

    /**
     * @param int $userId
     * @return bool
     */
    public function forget(int $userId): bool;
}
