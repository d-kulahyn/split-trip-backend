<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\DebtStatusEnum;
use App\Domain\Exception\GroupHasDebtsException;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Domain\ValueObject\Balance;
use App\Domain\Services\DebtDistributor;

class Group
{

    /**
     * @param string $name
     * @param string $category
     * @param int $createdBy
     * @param string $finalCurrency
     * @param bool $simplifyDebts
     * @param string|null $id
     * @param string|null $avatar
     * @param array $debts
     * @param array $members
     * @param array $expenses
     * @param array $expensesToDelete
     */
    public function __construct(
        public string $name,
        public string $category,
        public int $createdBy,
        public string $finalCurrency,
        public bool $simplifyDebts = true,
        public ?string $id = null,
        public ?string $avatar = null,
        public array $debts = [],
        private array $members = [],
        private array $expenses = [],
        private array $expensesToDelete = []
    ) {}

    /**
     * @param Customer $member
     *
     * @return $this
     */
    public function addMember(Customer $member): self
    {
        if ($member->id) {
            $this->members[$member->id] = $member;

            return $this;
        }

        $this->members[] = $member;

        return $this;
    }

    /**
     * @param int $memberId
     *
     * @return bool
     */
    public function hasMember(int $memberId): bool
    {
        return isset($this->members[$memberId]);
    }

    /**
     * @param Expense $expense
     *
     * @return void
     */
    public function addExpense(Expense $expense): void
    {
        if ($expense->id) {
            $this->expenses[$expense->id] = $expense;

            return;
        }

        $this->expenses[] = $expense;
    }

    /**
     * @return array
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * @return array<Expense>
     */
    public function getExpenses(): array
    {
        return $this->expenses;
    }

    /**
     * @return bool
     */
    public function hasMembers(): bool
    {
        return count($this->members) > 0;
    }

    /**
     * @return array
     */
    public function getMemberIds(): array
    {
        header('Content-Type: application/json');
        echo json_encode($this->members);die;
        return array_map(fn(Customer $member) => $member->id, $this->members);
    }

    /**
     * @param array $memberIds
     *
     * @return void
     */
    public function removeMember(array $memberIds = []): void
    {
        foreach ($memberIds as $memberId) {
            unset($this->members[$memberId]);
        }
    }

    /**
     * @return array<Balance>
     */
    public function getBalances(array $memberIds = []): array
    {
        $balances = [];
        foreach ($this->getMemberIds() as $memberId) {
            if (!empty($memberIds) && !in_array($memberId, $memberIds)) {
                continue;
            }

            if (!isset($balances[$memberId])) {
                $balances[$memberId] = new Balance();
            }

            foreach ($this->getDebts() as $debt) {
                if ($debt->status === DebtStatusEnum::PAID) {

                    continue;
                }

                if ($debt->from === $memberId) {
                    $balances[$memberId]->balance = (float)bcsub(
                        (string)$balances[$memberId]->balance,
                        (string)$debt->amount
                    );
                    $balances[$memberId]->owe = (float)bcadd(
                        (string)$balances[$memberId]->owe,
                        (string)$debt->amount
                    );

                    continue;
                }

                if ($debt->to === $memberId) {
                    $balances[$memberId]->balance = (float)bcadd(
                        (string)$balances[$memberId]->balance,
                        (string)$debt->amount
                    );
                    $balances[$memberId]->paid = (float)bcadd(
                        (string)$balances[$memberId]->paid,
                        (string)$debt->amount
                    );
                }
            }
        }

        return $balances;
    }

    /**
     * @param array<Customer> $members
     *
     * @return void
     */
    public function setMembers(array $members): void
    {
        $this->members = $members;
    }

    /**
     * @return array<Debt>
     */
    public function distributeDebts(): array
    {
        $balances = collect($this->getBalances());
        /** @var DebtDistributor $debtsDistributor */
        $debtsDistributor = app(DebtDistributor::class);

        return $debtsDistributor->distributeDebts($balances, $this->finalCurrency, $this->id);
    }

    /**
     * @param int $customerId
     *
     * @return Balance
     */
    public function getMyGeneralStatistic(int $customerId): Balance
    {
        $balance = new Balance();

        foreach ($this->getExpenses() as $expense) {
            foreach($expense->getPayers() as $payer) {
                if ($payer->payerId === $customerId) {
                    $balance->paid = (float)bcadd((string)$balance->paid, (string)$payer->amount);
                }
            }

            foreach($expense->getDebtors() as $debtor) {
                if ($debtor->debtorId === $customerId) {
                    $balance->owe = (float)bcadd((string)$balance->owe, (string)$debtor->amount);
                }
            }
        }

        $balance->balance = (float)bcsub((string)$balance->paid, (string)$balance->owe);

        return $balance;
    }

    /**
     * @return array<Debt>
     */
    public function getDebts(): array
    {
        $debts = [];

        foreach ($this->getExpenses() as $expense) {
            foreach ($expense->getDebts() as $debt) {
                $debts[] = $debt;
            }
        }

        return array_merge($this->debts, $debts);
    }

    /**
     * @param int $id
     *
     * @return Expense|null
     */
    public function getExpenseById(int $id): ?Expense
    {
        foreach ($this->expenses as $expense) {
            if ($expense->id === $id) {
                return $expense;
            }
        }

        return null;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function removeExpense(int $id): void
    {
        foreach ($this->expenses as $key => $expense) {
            if ($expense->id === $id) {
                $this->expensesToDelete[] = $expense;
                unset($this->expenses[$key]);
            }
        }
    }

    /**
     * @return array<Expense>
     */
    public function getExpensesToDelete(): array
    {
        return $this->expensesToDelete;
    }

    /**
     * @throws GroupHasDebtsException
     */
    public function remove(): bool
    {
        if (!empty($this->getDebts())) throw new GroupHasDebtsException();

        /** @var GroupWriteRepositoryInterface $groupWriteRepository */
        $groupWriteRepository = app(GroupWriteRepositoryInterface::class);

        return $groupWriteRepository->remove($this);
    }
}
