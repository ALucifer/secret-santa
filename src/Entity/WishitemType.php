<?php

namespace App\Entity;

enum WishitemType: string
{
    case MONEY = 'MONEY';
    case EVENT = 'EVENT';
    case GIFT = 'GIFT';

    public static function values(): array
    {
        return array_map(
            fn($i) => $i->value,
            self::cases()
        );
    }
}