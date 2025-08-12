<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use Illuminate\Http\Request;
use App\Domain\Entity\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        /** @var Transaction $resource */
        $resource = $this->resource;

        return [
            'id'         => $resource->id,
            'from'       => new CustomerResource($resource->from),
            'to'         => new CustomerResource($resource->to),
            'group_id'   => $resource->groupId,
            'group_name' => $resource->groupName,
            'amount'     => $resource->amount,
            'currency'   => $resource->currency,
            'status'     => $resource->status->value,
        ];
    }
}
