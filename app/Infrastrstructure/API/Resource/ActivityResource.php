<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        /** @var ActivityLog $resource */
        $resource = $this->resource;

        return [
            'id'          => $resource->id,
            'customer_id' => $resource->customerId,
            'group_id'    => $resource->groupId,
            'group'       => new GroupResource($resource->group),
            'action_type' => $resource->actionType,
            'details'     => $resource->details,
            'created_at'  => $resource->createdAt,
            'customer'    => $resource->customer ? new CustomerResource($resource->customer) : null,
        ];
    }
}
