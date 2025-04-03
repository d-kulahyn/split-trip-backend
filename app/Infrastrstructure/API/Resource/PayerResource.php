<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Payer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PayerResource extends JsonResource
{

    public function toArray($request): array
    {
        /** @var Payer $resource */
        $resource = $this->resource;

        return [
            'id'     => $resource->id,
            'avatar' => $resource->avatar !== null ? Storage::url($resource->avatar) : null,
        ];
    }
}
