<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Application\DebtException;
use App\Domain\Enum\StatusEnum;
use App\Domain\Exception\GroupHasDebtsException;
use App\Domain\Repository\DebtWriteRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use App\Domain\ValueObject\Balance;
use App\Domain\Services\DebtDistributor;

class Group
{

    /**
     * @param string $name
     * @param string $category
     * @param int $createdBy
     * @param string $finalCurrency
     * @param DebtWriteRepositoryInterface $debtWriteRepository
     * @param TransactionWriteRepositoryInterface $transactionWriteRepository
     * @param bool $simplifyDebts
     * @param string|null $id
     * @param string|null $avatar
     * @param array $debts
     * @param array<Customer> $members
     * @param array $expenses
     * @param array $expensesToDelete
     */
    public function __construct(
        public string $name,
        public string $category,
        public int $createdBy,
        public string $finalCurrency,
        public DebtWriteRepositoryInterface $debtWriteRepository,
        public TransactionWriteRepositoryInterface $transactionWriteRepository,
        public bool $simplifyDebts = true,
        public ?string $id = null,
        public ?string $avatar = null,
        private readonly array $debts = [],
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
        $this->members[$member->id] = $member;

        return $this;
    }

    /**
     * @param int $memberId
     *
     * @return bool
     */
    public function hasMember(int $memberId): bool
    {
        return (bool)count(array_filter($this->members, fn(Customer $member) => $member->id === $memberId));
    }

    /**
     * @param Expense $expense
     *
     * @return void
     */
    public function addExpense(Expense $expense): void
    {
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
     * @param int $id
     *
     * @return Customer|null
     */
    public function getMember(int $id): ?Customer
    {
        foreach ($this->members as $member) {
            if ($member->id === $id) {
                return $member;
            }
        }

        return null;
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
                $balances[$memberId] = new Balance(customerId: $memberId);
            }

            foreach ($this->getDebts() as $debt) {
                if ($debt->status === StatusEnum::PAID) {

                    continue;
                }

                if ($debt->from->id === $memberId) {
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

                if ($debt->to->id === $memberId) {
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
        if (empty($this->getBalances())) return [];

        $balances = collect($this->getBalances());
        /** @var DebtDistributor $debtsDistributor */
        $debtsDistributor = app(DebtDistributor::class);

        return $debtsDistributor->distributeDebts($balances, $this->finalCurrency, $this->id);
    }

    /**
     * @throws DebtException
     */
    public function updateDebtAmount(int $debtId, float $amount): void
    {
        $debt = $this->getDebtById($debtId);

        if (!$debt) {
            throw new DebtException('Debt not found.');
        }

        if ($amount <= 0 || $amount > $debt->amount) {
            throw new DebtException('Invalid amount provided.');
        }

        $debt->subAmount($amount);

        if ($debt->amount === 0.00) {
            $debt->status = StatusEnum::PAID;
        }

        $this->debtWriteRepository->save($debt);
    }

    /**
     * @param int $customerId
     *
     * @return Balance
     */
    public function getGroupBalance(int $customerId): Balance
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

    public function getDebtById(int $id): ?Debt
    {
        foreach ($this->getDebts() as $debt) {
            if ($debt->id === $id) {
                return $debt;
            }
        }

        return null;
    }

    public function getDebtBetween(int $fromId, int $toId): ?Debt
    {
        foreach ($this->getDebts() as $debt) {
            if ($debt->from->id === $fromId && $debt->to->id === $toId) {
                return $debt;
            }
        }

        return null;
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

    public function __get(string $name)
    {
        return collect($this->$name());
    }
}
