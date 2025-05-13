<?php

namespace App\Doctrine;

use App\Entity\State;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class StateType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'state_enum';
    }

    /**
     * @param string $value
     * @param AbstractPlatform $platform
     * @return State
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return State::from($value);
    }

    /**
     * @param State $value
     * @param AbstractPlatform $platform
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value->value;
    }

    public function getName()
    {
        return 'state';
    }
}