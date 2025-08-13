<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;

use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\ActivityReadRepositoryInterface;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Infrastrstructure\API\DTO\StatusUpdateBatchDTO;
use App\Infrastrstructure\API\Resource\ActivityResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityController
{

    public function __construct(
        private readonly ActivityReadRepositoryInterface $activityReadRepository,
        private readonly ActivityWriteRepositoryInterface $activityWriteRepository
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ActivityResource::collection($this->activityReadRepository->list(StatusEnum::PENDING, auth()->id(), ['group', 'customer']));
    }

    public function statusBatch(StatusUpdateBatchDTO $statusUpdateBatchDTO): \Illuminate\Http\JsonResponse
    {
        $this->activityWriteRepository->updateStatuses($statusUpdateBatchDTO->ids, $statusUpdateBatchDTO->status);

        return response()->json([
            'message' => 'Statuses updated successfully',
            'status' => $statusUpdateBatchDTO->status->value,
        ]);
    }
}
