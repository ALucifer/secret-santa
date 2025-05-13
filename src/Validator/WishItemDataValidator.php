<?php

namespace App\Validator;

use App\Entity\WishitemType;
use App\Services\Request\DTO\NewWishItem;
use App\Validator\Constraints\WishEvent;
use App\Validator\Constraints\WishGift;
use App\Validator\Constraints\WishItemData;
use App\Validator\Constraints\WishMoney;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class WishItemDataValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof WishItemData) {
            throw new UnexpectedTypeException($constraint, WishItemData::class);
        }

        if (!$value instanceof NewWishItem) {
            throw new UnexpectedTypeException($value, NewWishItem::class);
        }

        $wishitemTypeConstraint = match ($value->type) {
            WishitemType::EVENT->value => new WishEvent(),
            WishitemType::MONEY->value => new WishMoney(),
            WishitemType::GIFT->value => new WishGift(),
            default => throw new LogicException(sprintf('%s validation is not supported', $value->type)),
        };

        $errors = $this
            ->context
            ->getValidator()
            ->validate(
                $value->data,
                $wishitemTypeConstraint
            );

        if ($errors->count() > 0) {
            $this->handleErrors($errors);
        }
    }

    private function handleErrors(ConstraintViolationListInterface $errors): void
    {
        foreach ($errors as $error) {
            $this
                ->context
                ->buildViolation($error->getMessage())
                ->atPath(sprintf('data%s', $error->getPropertyPath()))
                ->addViolation();
        }
    }
}