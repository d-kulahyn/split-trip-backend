<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Group;

interface GroupReadRepositoryInterface
{
    public function findById(string $id, ?array $with = null): ?Group;
    public function list(int $customerId): array;
    public function members(string $groupId): array;
    public function isAMember(string $groupId, int $customerId): bool;
    public function getNameById(string $id): ?string;
}
