<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\ActivityLog;
use App\Domain\Entity\Customer;
use App\Domain\Entity\Group;
use App\Domain\Entity\Payer;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Expense;
use App\Domain\Enum\ActivityLogActionTypeEnum;
use App\Domain\Enum\StatusEnum;
use App\Domain\Event\ActivityCreatedEvent;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Infrastrstructure\Notification\Messages\ExpenseAddedMessage;
use App\Infrastrstructure\Notification\PushNotification;
use App\Jobs\NotificationJob;
use App\Shared\Helper\CurrencyHelper;
use Illuminate\Support\Facades\DB;
use App\Infrastrstructure\API\DTO\ExpenseDTO;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Infrastrstructure\Service\CurrencyConverterService;
use App\Infrastrstructure\API\Exceptions\UnauthorizedGroupActionException;

class AddExpenseUseCase
{

    private array $notificationChannels = [
        PushNotification::class,
    ];

    /**
     * @param GroupReadRepositoryInterface $groupReadRepository
     * @param GroupWriteRepositoryInterface $groupWriteRepository
     * @param CurrencyConverterService $currencyConverterService
     * @param ActivityWriteRepositoryInterface $activityWriteRepository
     * @param CustomerReadRepositoryInterface $customerReadRepository
     */
    public function __construct(
        private readonly GroupReadRepositoryInterface $groupReadRepository,
        private readonly GroupWriteRepositoryInterface $groupWriteRepository,
        private readonly CurrencyConverterService $currencyConverterService,
        private readonly ActivityWriteRepositoryInterface $activityWriteRepository,
        private readonly CustomerReadRepositoryInterface $customerReadRepository,
    ) {}

    /**
     * @param ExpenseDTO $expenseDTO
     * @param string $groupId
     * @param int $customerId
     *
     * @throws UnauthorizedGroupActionException
     *
     * @return Group
     */
    public function execute(ExpenseDTO $expenseDTO, string $groupId, int $customerId): Group
    {
        $result = DB::transaction(function () use ($expenseDTO, $groupId, $customerId) {
            $group = $this->groupReadRepository->findById($groupId, lock: true);

            if (!$group->hasMember($customerId)) {
                return false;
            }

            $expense = new Expense(
                category   : $expenseDTO->category,
                createdAt  : $expenseDTO->created_at,
                currency   : $expenseDTO->currency,
                description: $expenseDTO->description,
                groupId    : $group->id,
            );

            foreach ($expenseDTO->debtors as $debtor) {
                $expense->addDebtor(new Debtor(
                    amount  : $debtor->amount,
                    debtorId: $debtor->id,
                    currency: $expenseDTO->currency,
                ));
            }

            foreach ($expenseDTO->payers as $payer) {
                $expense->addPayer(new Payer(
                    amount  : $payer->amount,
                    currency: $payer->currency,
                    payerId : $payer->id
                ));
            }

            $expense->distributeDebts(
                $this->currencyConverterService,
                $group->finalCurrency
            );
            $group->addExpense($expense);

            $this
                ->groupWriteRepository
                ->save($group);

            /** @var Customer $whoAdded */
            $whoAdded = $this->customerReadRepository->findById([$customerId])->first();

            //TODO: generate event for expense creation
            $activityLog = $this->activityWriteRepository->save(new ActivityLog(
                groupId    : $groupId,
                groupName  : $group->name,
                actionType : ActivityLogActionTypeEnum::EXPENSE_ADDED_TO_GROUP,
                customerIds: $group->getMemberIds(),
                createdBy  : $whoAdded,
                status     : StatusEnum::PENDING,
                details    : [
                    'amount' => $expense->credits(),
                ]
            ));

            foreach ($group->getMemberIds->reject(fn(int $id) => $id === $customerId)->toArray() as $memberId) {
                ActivityCreatedEvent::dispatch($memberId, $activityLog);
                NotificationJob::dispatch($this->notificationChannels, new ExpenseAddedMessage([
                    'amount'        => CurrencyHelper::currency_symbol($expense->credits(), $group->finalCurrency),
                    'customer_name' => $whoAdded->name,
                    'date'          => $expense->createdAt,
                    'group_name'    => $group->name,
                    'token'         => $group->getMember($memberId)->firebase_cloud_messaging_token,
                ]));
            }

            return $group;
        });

        if (!$result) {
            throw new UnauthorizedGroupActionException('You are not allowed to add expenses.');
        }

        return $result;
    }
}
