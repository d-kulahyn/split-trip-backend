<?php

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Debt;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    /**
     * @param $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Debt $resource */
        $resource = $this->resource;

        return [
            'id'       => $resource->id,
            'amount'   => $resource->amount,
            'currency' => $resource->currency,
            'groupId'  => $resource->groupId,
            'from'     => new CustomerResource($resource->from),
            'to'       => new CustomerResource($resource->to),
            'status'   => $resource->status->value,
        ];
    }
}
