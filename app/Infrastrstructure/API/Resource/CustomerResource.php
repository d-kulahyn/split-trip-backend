<?php

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Customer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CustomerResource extends JsonResource
{
    /**
     * @param $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Customer $resource */
        $resource = $this->resource;

        return [
            'id'                   => $resource->id,
            'name'                 => $resource->name,
            'email'                => $resource->email,
            'currency'             => $resource->currency,
            'balance'              => $resource->getBalance()->toArray(),
            'avatar'               => $resource->avatar !== null ? Storage::url($resource->avatar) : null,
            'email_is_verified'    => !is_null($resource->email_verified_at),
            'email_notifications'  => $resource->email_notifications,
            'push_notifications'   => $resource->push_notifications,
            'debt_reminder_period' => $resource->debt_reminder_period,
            'avatar_color'         => $resource->avatar_color,
        ];
    }
}
