<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Group;

interface GroupWriteRepositoryInterface
{
    public function save(Group $group);
    public function remove(Group $group): bool;
}
