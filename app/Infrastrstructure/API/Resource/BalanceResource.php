<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Customer;
use App\Domain\Entity\Group;
use App\Domain\Repository\BalanceReadRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        /** @var BalanceReadRepositoryInterface $balanceReadRepository */
        $balanceReadRepository = app(BalanceReadRepositoryInterface::class);
        /** @var CustomerReadRepositoryInterface $customerReadRepository */
        $customerReadRepository = app(CustomerReadRepositoryInterface::class);

        /** @var Group $resource */
        $resource = $this->resource;

        $balances = $balanceReadRepository->getGroupBalances($resource->getMemberIds());

        $customers = array_map(fn(Customer $customer) => new CustomerResource($customer->setBalance($balances[$customer->id])), $customerReadRepository->findById($resource->getMemberIds())->all());

        return [
            'balances' => $customers,
            'overallBalance' => $balanceReadRepository->getOverallBalance(auth()->id()),
        ];
    }
}
