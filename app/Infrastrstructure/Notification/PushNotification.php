<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification;

use App\Domain\Notification\NotificationChannelInterface;
use App\Infrastrstructure\Notification\Messages\FirebaseCloudMessagingInterface;
use Kreait\Firebase\Factory;

class PushNotification implements NotificationChannelInterface
{
    /**
     * @param Factory $factory
     */
    public function __construct(
        protected Factory $factory
    ) {}

    /**
     */
    public function send(\ArrayObject $message): void
    {
        try {
            if (!is_subclass_of($message, FirebaseCloudMessagingInterface::class)) return;

            if (!$message->offsetGet('token')) return;

            $messaging = $this->factory->createMessaging();

            $messaging->send($message->fcm());
        } catch (\Throwable $exception) {
            //TODO: Add logger
        }
    }
}
