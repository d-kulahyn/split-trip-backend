<?php

declare(strict_types=1);

namespace App\Infrastrstructure\Notification;

use App\Domain\Notification\NotificationChannelInterface;
use App\Infrastrstructure\Notification\Messages\FirebaseCloudMessagingInterface;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;

class PushNotifications implements NotificationChannelInterface
{
    /**
     * @param Factory $factory
     */
    public function __construct(
        protected Factory $factory
    ) {}

    /**
     * @throws MessagingException
     * @throws FirebaseException
     */
    public function send(\ArrayObject $message): void
    {
        if (!is_subclass_of($message, FirebaseCloudMessagingInterface::class)) return;

        if (!$message->offsetGet('token')) return;

        $messaging = $this->factory->createMessaging();

        $messaging->send($message->fcm());
    }
}
