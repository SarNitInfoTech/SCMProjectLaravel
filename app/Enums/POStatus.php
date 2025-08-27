<?php

namespace App\Enums;

enum POStatus: string
{
    case CANCEL = 'Cancel';
    case PENDING = 'Pending';
    case CLOSE = 'Close';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
