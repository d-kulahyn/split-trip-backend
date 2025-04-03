<?php

namespace App\Domain\Notification;

interface NotificationChannelInterface
{
    public function send(\ArrayObject $message);
}
