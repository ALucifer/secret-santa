<?php

namespace App\Doctrine;

use App\Entity\WishitemType as WishitemTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class WishitemType extends Type
{

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "wishitem_enum";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): WishitemTypeEnum
    {
        return WishitemTypeEnum::from($value);
    }

    /**
     * @param WishitemTypeEnum $value
     * @param AbstractPlatform $platform
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value->value;
    }

    public function getName(): string
    {
        return 'wishitem_enum';
    }
}