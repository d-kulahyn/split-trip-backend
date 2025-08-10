<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\ActivityReadRepositoryInterface;
use App\Infrastrstructure\API\Resource\ActivityResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController
{

    public function __construct(
        private readonly ActivityReadRepositoryInterface $activityReadRepository
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ActivityResource::collection($this->activityReadRepository->list(StatusEnum::PENDING, auth()->id(), ['group', 'customer']));
    }
}
