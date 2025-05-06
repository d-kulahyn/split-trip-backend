<?php

declare(strict_types=1);

namespace App\Infrastrstructure\API\Controllers;


use App\Domain\Entity\Customer;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Infrastrstructure\Notification\EmailNotification;
use App\Infrastrstructure\Notification\PushNotifications;
use App\Models\ExpenseDebt;
use Illuminate\Http\JsonResponse;
use App\Infrastrstructure\Notification\Messages\RemindDebtMessage;

readonly class NotificationController
{
    /**
     * @param CustomerReadRepositoryInterface $customerReadRepository
     */
    public function __construct(
        protected CustomerReadRepositoryInterface $customerReadRepository
    ) {}

    /**
     * @param ExpenseDebt $debt
     *
     * @return JsonResponse
     */
    public function debtReminder(ExpenseDebt $debt): JsonResponse
    {
        $channels = [
            app(EmailNotification::class),
            app(PushNotifications::class),
        ];

        /** @var Customer $customer */
        $customer = $this->customerReadRepository->findById([$debt->to])->first();

        file_put_contents('/var/www/html/log.log', print_r($customer->toArray(), true), FILE_APPEND);

        foreach ($channels as $channel) {
            $channel->send(
                new RemindDebtMessage([
                    'amount'       => $debt->amount,
                    'currency'     => $debt->currency,
                    'groupName'    => $debt->group->name,
                    'creditorName' => $debt->creditor->name,
                    'token'        => $customer->firebase_cloud_messaging_token,
                ])
            );
        }

        return response()->json(['message' => 'Notification was sent successfully!']);
    }
}
