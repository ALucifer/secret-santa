<?php

namespace App\Validator\Constraints;

use App\Validator\WishGiftValidator;
use Symfony\Component\Validator\Constraint;

class WishGift extends Constraint
{
    public function validatedBy(): string
    {
        return WishGiftValidator::class;
    }
}