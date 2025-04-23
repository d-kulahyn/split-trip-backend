<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Debtor;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DebtorResource extends JsonResource
{

    public function toArray($request): array
    {
        /** @var Debtor $resource */
        $resource = $this->resource;

        return [
            'id'     => $resource->id,
            'name'   => $resource->name,
            'amount' => $resource->amount,
            'avatar' => $resource->avatar !== null ? Storage::url($resource->avatar) : null,
        ];
    }
}
