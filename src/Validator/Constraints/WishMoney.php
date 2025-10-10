<?php

namespace App\Validator\Constraints;

use App\Validator\WishMoneyValidator;
use Symfony\Component\Validator\Constraint;

class WishMoney extends Constraint
{
    public function validatedBy(): string
    {
        return WishMoneyValidator::class;
    }
}
