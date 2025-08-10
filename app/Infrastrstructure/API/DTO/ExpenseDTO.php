<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\DTO;

use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Infrastrstructure\API\Rules\MembersInGroup;
use App\Infrastrstructure\API\Rules\PayersAndDebtorsAmountMatch;
use App\Models\Group;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\LaravelData\Data;

/**
 * @property DebtorDTO[] $debtors
 * @property PayerDTO[] $payers
 */
class ExpenseDTO extends Data
{
    /**
     * @param string $description
     * @param string $category
     * @param string $currency
     * @param array $debtors
     * @param array $payers
     * @param int $created_at
     * @param StatusEnum $status
     */
    public function __construct(
        public readonly string $description,
        public readonly string $category,
        public readonly string $currency,
        public readonly array $debtors,
        public readonly array $payers,
        public readonly int $created_at,
        public readonly StatusEnum $status = StatusEnum::PENDING,
    ) {}

    /**
     * @param ...$args
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return array
     */
    public static function rules(...$args): array
    {
        /** @var Group $group */
        $group = request()->route('group');

        /** @var GroupReadRepositoryInterface $groupRep */
        $groupRep = app(GroupReadRepositoryInterface::class);

        $group = $groupRep->findById($group->id);

        $payers = request()->get('payers', []);
        $debtors = request()->get('debtors', []);

        return [
            'description'      => ['required', 'string', 'max:255'],
            'category'         => ['required', 'string', 'max:255'],
            'payers'           => [
                'required',
                'array',
                new MembersInGroup($group),
                new PayersAndDebtorsAmountMatch($payers, $debtors),
            ],
            'status'           => ['required', 'string', 'in:'.implode(',', StatusEnum::values())],
            'payers.*.id'      => ['required', 'exists:customers,id'],
            'currency'         => ['required', 'string'],
            'payers.*.amount'  => ['required', 'numeric', 'min:0'],
            'debtors.*.amount' => ['required', 'numeric', 'min:0'],
            'debtors'          => ['required', 'array', new MembersInGroup($group)],
            'created_at'       => ['required', 'int'],
            'debtors.*.id'     => ['required', 'exists:customers,id'],
        ];
    }
}
