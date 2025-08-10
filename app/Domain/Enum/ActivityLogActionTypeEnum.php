<?php

namespace App\Domain\Enum;

enum ActivityLogActionTypeEnum: string
{
    case MEMBER_ADDED_TO_GROUP = 'MEMBER_ADDED_TO_GROUP';
    case TRANSACTION_UPDATED = 'TRANSACTION_UPDATED';
    case DEBT_REMINDER_SENT = 'DEBT_REMINDER_SENT';
    case EXPENSE_ADDED_TO_GROUP = 'EXPENSE_ADDED_TO_GROUP';
}
