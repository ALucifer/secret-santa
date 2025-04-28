<?php

namespace App\Validator\Constraints;

use App\Validator\WishItemDataValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class WishItemData extends Constraint
{
    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return WishItemDataValidator::class;
    }
}