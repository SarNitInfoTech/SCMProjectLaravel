<?php

namespace App\Enums;

enum MovementType: string
{
    case IN = 'IN';
    case OUT = 'OUT';
    case ADJUST = 'ADJUST';
    case RETURN = 'RETURN';
    case TRANSFER = 'TRANSFER';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
