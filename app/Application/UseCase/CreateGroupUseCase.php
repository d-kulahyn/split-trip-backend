<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Dto\CreateGroupDto;
use App\Domain\Entity\Group;
use App\Domain\Factory\GroupFactory;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Infrastrstructure\API\DTO\RequestGroupDTO;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use InvalidArgumentException;

readonly class CreateGroupUseCase
{
    /**
     * @param GroupFactory $groupFactory
     * @param CustomerReadRepositoryInterface $customerReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     */
    public function __construct(
        public GroupFactory $groupFactory,
        public CustomerReadRepositoryInterface $customerReadRepository,
        public GroupWriteRepositoryInterface $groupWriteRepository
    ) {}

    /**
     * @param RequestGroupDTO $groupDTO
     *
     * @return Group
     */
    public function execute(RequestGroupDTO $groupDTO): Group
    {
        if (is_null($groupDTO->created_by)) {
            throw new InvalidArgumentException('Customer not found');
        }

        $customer = $this->customerReadRepository->findById([$groupDTO->created_by])->first();

        if (is_null($customer)) {
            throw new InvalidArgumentException('Customer not found');
        }

        $group = $this->groupFactory->create(new CreateGroupDto(
            name         : $groupDTO->name,
            currency     : $groupDTO->currency,
            simplifyDebts: true,
            category     : $groupDTO->category,
            members      : [$customer]
        ), $groupDTO->created_by);

        $this->groupWriteRepository->save($group);

        return $group;
    }
}
