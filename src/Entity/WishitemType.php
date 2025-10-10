<?php

namespace App\Entity;

enum WishitemType: string
{
    case MONEY = 'MONEY';
    case EVENT = 'EVENT';
    case GIFT = 'GIFT';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(
            fn ($item) => $item->value,
            self::cases()
        );
    }
}
