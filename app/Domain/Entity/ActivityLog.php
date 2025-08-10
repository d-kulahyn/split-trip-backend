<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Enum\StatusEnum;

class ActivityLog
{
    /**
     * @param int $customerId
     * @param string $groupId
     * @param ActivityLogActionTypeEnum $actionType
     * @param Customer|null $customer
     * @param int|null $createdAt
     * @param StatusEnum $status
     * @param array $details
     * @param Group|null $group
     * @param int|null $id
     */
    public function __construct(
        public int $customerId,
        public string $groupId,
        public ActivityLogActionTypeEnum $actionType,
        public ?Customer $customer = null,
        public ?int $createdAt = null,
        public StatusEnum $status = StatusEnum::PENDING,
        public array $details = [],
        public ?Group $group = null,
        public ?int $id = null
    ) {}
}
