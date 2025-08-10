<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\DebtReminderPeriodEnum;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\ValueObject\Balance;
use App\Infrastrstructure\Service\CurrencyConverterService;
use Spatie\LaravelData\Data;

class Customer extends Data
{
    /**
     * @param string $password
     * @param string $email
     * @param array $friends
     * @param bool $email_notifications
     * @param bool $push_notifications
     * @param DebtReminderPeriodEnum $debt_reminder_period
     * @param string|null $firebase_cloud_messaging_token
     * @param string|null $social_type
     * @param string|null $social_id
     * @param string|null $email_verified_at
     * @param string|null $name
     * @param string|null $avatar
     * @param int|null $id
     * @param string|null $currency
     * @param Balance|null $balance
     * @param string|null $avatar_color
     */
    public function __construct(
        public string $password,
        public string $email,
        public array $friends = [],
        public bool $email_notifications = true,
        public bool $push_notifications = true,
        public DebtReminderPeriodEnum $debt_reminder_period = DebtReminderPeriodEnum::WEEKLY,
        public ?string $firebase_cloud_messaging_token = null,
        public ?string $social_type = null,
        public ?string $social_id = null,
        public ?string $email_verified_at = null,
        public ?string $name = null,
        public ?string $avatar = null,
        public ?int $id = null,
        public ?string $currency = null,
        public ?Balance $balance = null,
        public ?string $avatar_color = null,
    ) {}

    /**
     * @param int $friendId
     *
     * @return bool
     */
    public function hasFriend(int $friendId): bool
    {
        return in_array($friendId, $this->friends);
    }

    /**
     * @param int $friendId
     *
     * @return void
     */
    public function addFriend(int $friendId): void
    {
        $this->friends[] = $friendId;
    }

    /**
     * @param Balance $balance
     *
     * @return $this
     */
    public function setBalance(Balance $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Balance
     */
    public function getBalance(): Balance
    {
        if ($this->balance) {
            return $this->balance;
        }

        $customerReadRepository = app(CustomerReadRepositoryInterface::class);
        $currencyConverterService = app(CurrencyConverterService::class);

        $groups = $customerReadRepository->groups($this->id);

        $generalBalance = new Balance(
            owe    : 0,
            paid   : 0,
            balance: 0,
            customerId: $this->id,
        );

        /** @var Group $group */
        foreach ($groups as $group) {
            $balances = $group->getBalances([$this->id]);

            /** @var ?Balance $balance */
            $balance = $balances[$this->id] ?? null;

            if ($balance) {
                $balance->owe = $currencyConverterService->convert($group->finalCurrency, $balance->owe, $this->currency);
                $balance->paid = $currencyConverterService->convert($group->finalCurrency, $balance->paid, $this->currency);
                $balance->balance = (float)bcsub((string)$balance->paid, (string)$balance->owe);

                $generalBalance->owe = (float)bcadd((string)$generalBalance->owe, (string)$balance->owe);
                $generalBalance->paid = (float)bcadd((string)$generalBalance->paid, (string)$balance->paid);
                $generalBalance->balance = (float)bcadd((string)$generalBalance->balance, (string)$balance->balance);
            }
        }

        $this->balance = $generalBalance;

        return $generalBalance;
    }
}
