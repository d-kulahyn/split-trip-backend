<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Resource;

use App\Domain\Entity\Group;
use App\Domain\Entity\Customer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * @param $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Group $resource */
        $resource = $this->resource;

        $overallBalances = $resource->getMembers->map(fn (Customer $customer) => $customer->getBalance())->toArray();

        if (!$resource->getBalanceOf(35)) {
            header('Content-Type: application/json');
            echo json_encode([
                $resource->getMemberIds(),
                $resource->getBalances()
            ]);die;
        }

        $customersWithGroupBalances = $resource->getMembers->map(fn (Customer $customer) => $customer->setBalance($resource->getBalanceOf($customer->id)))->toArray();

        $members = $resource->getMembers();

        return [
            'debts'            => DebtResource::collection($resource->getDebts()),
            'id'               => $resource->id,
            'name'             => $resource->name,
            'category'         => $resource->category,
            'final_currency'   => $resource->finalCurrency,
            'created_by'       => $resource->createdBy,
            'members'          => $members ? CustomerResource::collection($members) : [],
            'expenses'         => ExpenseResource::collection($resource->getExpenses()),
            'my_balance'       => $resource->getBalanceOf(auth()->id()),
            'simplify_debts'   => $resource->simplifyDebts,
            'balances'         => $customersWithGroupBalances,
            'avatar'           => $resource->avatar !== null ? Storage::url($resource->avatar) : null,
            'overall_balance'  => $members[request()->user()->id]->getBalance(),
            'overall_balances' => $overallBalances,
        ];
    }
}
