<?php

namespace App\Enums;

enum POStatus: string
{
    case OPEN = 'Open';
    case PARTIALLY_RECEIVED = 'Partially Received';
    case COMPLETED = 'Completed';
    case CLOSED = 'Closed';
    case REOPENED = 'Reopened';
    case CANCEL = 'Cancel';
    case PENDING = 'Pending';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
