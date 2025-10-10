<?php

namespace App\Validator\Constraints;

use App\Validator\WishEventValidator;
use Symfony\Component\Validator\Constraint;

class WishEvent extends Constraint
{
    public function validatedBy(): string
    {
        return WishEventValidator::class;
    }
}
