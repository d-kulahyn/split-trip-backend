<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Debtor;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtorResource extends JsonResource
{

    public function toArray($request): array
    {
        /** @var Debtor $resource */
        $resource = $this->resource;

        return [
            'id'     => $resource->debtorId,
            'name'   => $resource->name,
            'amount' => $resource->amount,
            'currency' => $resource->currency,
            'avatar_color' => $resource->avatarColor,
        ];
    }
}
