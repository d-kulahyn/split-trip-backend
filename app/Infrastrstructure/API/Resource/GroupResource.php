<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Customer;
use App\Domain\Entity\Debt;
use App\Domain\Entity\Group;
use App\Domain\Repository\CurrencyReadRepositoryInterface;
use App\Domain\ValueObject\Balance;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Domain\Repository\CustomerReadRepositoryInterface;
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

        $customers = $customerReadRepository->findById($resource->getMemberIds());

        $overallBalance = $customers[auth()->id()]->getBalance();

        $overallBalances = [];
        /** @var Customer $customer */
        foreach ($customers as $customer)  {
            $overallBalances[$customer->id] = $customer->getBalance();
        }

        $balances = $resource->hasMembers() ? array_map(function (Balance $balance, int $customerId) use (
            $customers
        ) {
            $customers[$customerId]->setBalance($balance);

            return new CustomerResource($customers[$customerId]);
        }, $resource->getBalances(), array_keys($resource->getBalances())) : [];

        usort($balances, function (CustomerResource $a, CustomerResource $b) {
            if ($a->resource->id == auth()->id()) {
                return -1;
            }
            if ($b->resource->id == auth()->id()) {
                return 1;
            }

            return $b->resource->getBalance()->balance <=> $a->resource->getBalance()->balance;
        });

        $debts = $resource->hasMembers() ? array_map(function (Debt $debt) use ($customers) {
            return [
                'from'     => new CustomerResource($customers[$debt->from]),
                'to'       => new CustomerResource($customers[$debt->to]),
                'amount'   => $debt->amount,
                'status'   => $debt->status->value,
                'id'       => $debt->id,
                'currency' => $debt->currency,
            ];
        }, $resource->getDebts()) : [];

        usort($debts, function ($a, $b) {
            if ($a['from']->resource->id == auth()->id()) {
                return -1;
            }
            if ($b['from']->resource->id == auth()->id()) {
                return 1;
            }

            return $b['amount'] <=> $a['amount'];
        });

        $myBalance = array_filter(
                         $balances,
                         function (CustomerResource $customer) {
                             return $customer->resource->id === auth()->id();
                         }
                     )[0];

        return [
            'id'             => $resource->id,
            'name'           => $resource->name,
            'category'       => $resource->category,
            'final_currency' => $resource->finalCurrency,
            'created_by'     => $resource->createdBy,
            'members'        => $resource->hasMembers() ? CustomerResource::collection($resource->getMembers()) : [],
            'currencies'     => $currencyReadRepository->codes(),
            'expenses'       => ExpenseResource::collection($resource->getExpenses()),
            'myBalance'      => $myBalance,
            'simplify_debts' => $resource->simplifyDebts,
            'balances'       => $balances,
            'debts'          => $debts,
            'rates'          => $currencyReadRepository->rates($resource->finalCurrency),
            'avatar'         => $resource->avatar !== null ? Storage::url($resource->avatar) : null,
            'overallBalance' => $overallBalance,
            'overallBalances' => $overallBalances,
        ];
    }
}
