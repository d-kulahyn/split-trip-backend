<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Customer;
use Illuminate\Support\Collection;

interface CustomerReadRepositoryInterface
{
    public function findByLogin(string $field, string $login): ?Customer;
    public function findByEmail(string $email): ?Customer;
    public function findById(array $ids, array $with = []): ?Collection;
    public function getCustomersWithoutSpecificFriends(array $customerId, array $friendId): ?Collection;
    public function getBySocialId(string $id, string $social): ?Customer;
    public function groups(int $customerId): Collection;
}
