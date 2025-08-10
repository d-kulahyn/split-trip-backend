<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Customer;
use App\Domain\Entity\Group;
use App\Domain\Repository\CurrencyReadRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\ValueObject\Balance;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GroupResource extends JsonResource
{
    /**
     * @param $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $currencyReadRepository = app(CurrencyReadRepositoryInterface::class);
        $customerReadRepository = app(CustomerReadRepositoryInterface::class);

        /** @var Group $resource */
        $resource = $this->resource;

        $members = $resource->getMembers();

        $balances = array_map(
            fn(Balance $balance) => $members[$balance->customerId]->setBalance($balance),
            $resource->getBalances()
        );


        $overallBalances = $customerReadRepository->findById($resource->getMemberIds())?->all() ?? [];
        $overallBalances = array_map(fn(Customer $customer) => $customer->getBalance(), $overallBalances);

        return [
            'debts'           => DebtResource::collection($resource->getDebts()),
            'id'              => $resource->id,
            'name'            => $resource->name,
            'category'        => $resource->category,
            'final_currency'  => $resource->finalCurrency,
            'created_by'      => $resource->createdBy,
            'members'         => $resource->hasMembers() ? CustomerResource::collection($resource->getMembers()) : [],
            'currencies'      => $currencyReadRepository->codes(),
            'expenses'        => ExpenseResource::collection($resource->getExpenses()),
            'myBalance'       => $balances[auth()->id()] ?? new Balance(),
            'simplify_debts'  => $resource->simplifyDebts,
            'balances'        => $balances,
            'rates'           => $currencyReadRepository->rates($resource->finalCurrency),
            'avatar'          => $resource->avatar !== null ? Storage::url($resource->avatar) : null,
            'overallBalance'  => isset($members[request()->user()->id]) ? $members[request()->user()->id]->getBalance() : new Balance(),
            'overallBalances' => $overallBalances,
        ];
    }
}
