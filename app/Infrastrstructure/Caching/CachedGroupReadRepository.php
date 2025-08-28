<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Caching;

use App\Domain\Entity\Group;
use App\Domain\Repository\GroupReadRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as Cache;

readonly class CachedGroupReadRepository implements GroupReadRepositoryInterface
{

    public function __construct(
        private GroupReadRepositoryInterface $inner,
        private Cache $cache,
        private int $ttlSeconds = 3600,
    ) {}

        public function list(int $customerId): array
        {
            $key = "customer:{$customerId}:groups";

            return $this->cache->remember($key, $this->ttlSeconds, fn () => $this->inner->list($customerId));
        }

    public function findById(string $id, ?array $with = null, bool $lock = false): ?Group
    {
        return $this->inner->findById($id, $with);
    }

    public function members(string $groupId): array
    {
        return $this->inner->members($groupId);
    }

    public function isAMember(string $groupId, int $customerId): bool
    {
        return $this->inner->isAMember($groupId, $customerId);
    }

    public function getNameById(string $id): ?string
    {
        return $this->inner->getNameById($id);
    }
}
