<?php

namespace App\Infrastrstructure\Notification\Messages;

use Illuminate\Contracts\Mail\Mailable;

interface EmailMessageInterface
{
    public function email(): Mailable;
}
