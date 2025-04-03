<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Group;

interface GroupReadRepositoryInterface
{
    public function findById(string $id): ?Group;
    public function list(int $customerId): array;
    public function members(string $groupId): array;
}
