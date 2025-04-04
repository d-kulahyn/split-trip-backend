<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Debt;
use App\Domain\Entity\Group;
use App\Domain\ValueObject\Balance;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CurrencyReadRepositoryInterface;
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

        $balances = $resource->hasMembers() ? array_map(function (Balance $balance, int $customerId) use (
            $customers
        ) {
            return [
                'owe'       => $balance->owe,
                'paid'       => $balance->paid,
                'balance'    => $balance->balance,
                'customer'   => new CustomerResource($customers[$customerId]),
                'customerId' => $customerId,

            ];
        }, $resource->getBalances(), array_keys($resource->getBalances())) : [];

        usort($balances, function ($a, $b) {
            if ($a['customerId'] == auth()->id()) {
                return -1;
            }
            if ($b['customerId'] == auth()->id()) {
                return 1;
            }

            return $b['balance'] <=> $a['balance'];
        });

        $debts = $resource->hasMembers() ? array_map(function (Debt $debt) use ($customers) {
            return [
                'from'   => new CustomerResource($customers[$debt->from]),
                'to'     => new CustomerResource($customers[$debt->to]),
                'amount' => $debt->amount,
                'status' => $debt->status->value,
                'id'     => $debt->id,
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

        return [
            'id'             => $resource->id,
            'name'           => $resource->name,
            'category'       => $resource->category,
            'final_currency' => $resource->finalCurrency,
            'created_by'     => $resource->createdBy,
            'members'        => $resource->hasMembers() ? CustomerResource::collection($resource->getMembers()) : [],
            'currencies'     => $currencyReadRepository->codes(),
            'expenses'       => ExpenseResource::collection($resource->getExpenses()),
            'myBalance'      => $resource->getBalances()[auth()->id()] ?? new Balance(),
            'simplify_debts' => $resource->simplifyDebts,
            'balances'       => $balances,
            'debts'          => $debts,
            'rates'          => $currencyReadRepository->rates($resource->finalCurrency),
            'avatar'         => $resource->avatar !== null ? Storage::url($resource->avatar) : null,
        ];
    }
}
