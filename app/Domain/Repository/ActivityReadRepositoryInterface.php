<?php

namespace App\Domain\Repository;

use App\Domain\Enum\StatusEnum;
use Illuminate\Support\Collection;

interface ActivityReadRepositoryInterface
{
    public function list(StatusEnum $status,  int $to): Collection;
}
