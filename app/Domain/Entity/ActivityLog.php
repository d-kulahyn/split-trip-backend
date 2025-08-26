<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\ActivityLogActionTypeEnum;

class ActivityLog
{
    /**
     * @param string $groupId
     * @param string $groupName
     * @param ActivityLogActionTypeEnum $actionType
     * @param array $customerIds
     * @param Customer|null $customer
     * @param int|null $createdAt
     * @param array $details
     * @param int|null $id
     */
    public function __construct(
        public string $groupId,
        public string $groupName,
        public ActivityLogActionTypeEnum $actionType,
        public array $customerIds = [],
        public ?Customer $customer = null,
        public ?int $createdAt = null,
        public array $details = [],
        public ?int $id = null
    ) {}
}
