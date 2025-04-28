<?php

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Expense;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * @param $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Expense $resource */
        $resource = $this->resource;

        return [
            'id'                     => $resource->id,
            'owe'                    => $resource->owe($request->user()->id),
            'paid'                   => $resource->paid($request->user()->id),
            'description'            => $resource->description,
            'currency'               => $resource->currency,
            'groupId'                => $resource->groupId,
            'created_at'             => $resource->createdAt,
            'category'               => $resource->category,
            'payers'                 => PayerResource::collection($resource->payers),
            'debtors'                => DebtorResource::collection($resource->debtors),
            'generalAmountOfAllPays' => $resource->credits(),
        ];
    }
}
