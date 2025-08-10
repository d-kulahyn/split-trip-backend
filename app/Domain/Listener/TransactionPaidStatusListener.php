<?php

declare(strict_types=1);

namespace App\Domain\Listener;

use App\Application\DebtException;
use App\Domain\Enum\StatusEnum;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Events\TransactionStatusUpdated;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class TransactionPaidStatusListener
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        private readonly GroupReadRepositoryInterface $groupReadRepository
    ) {}

    /**
     * @throws DebtException
     */
    public function handle(TransactionStatusUpdated $event): void
    {
        if ($event->transaction->status != StatusEnum::PAID) {
            return;
        }

        $group = $this->groupReadRepository->findById($event->transaction->groupId);

        if (!$group) {
            return;
        }

        $debt = $group->getDebtBetween($event->transaction->from->id, $event->transaction->to->id);

        if (!$debt) {
            return;
        }

        $group->updateDebtAmount($debt->id, $event->transaction->amount);
    }
}
