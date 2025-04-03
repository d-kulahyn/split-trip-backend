<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\ActivityLogActionTypeEnum;

class ActivityLog
{
    /**
     * @param int $customerId
     * @param string $groupId
     * @param ActivityLogActionTypeEnum $actionType
     * @param array $details
     * @param int|null $id
     */
    public function __construct(
        public int $customerId,
        public string $groupId,
        public ActivityLogActionTypeEnum $actionType,
        public array $details = [],
        public ?int $id = null
    ) {}
}
