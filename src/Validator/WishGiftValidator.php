<?php

namespace App\Validator;

use App\Validator\Constraints\WishGift;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraints as Assert;

class WishGiftValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof WishGift) {
            throw new UnexpectedTypeException($constraint, WishGift::class);
        }

        $errors = $this
            ->context
            ->getValidator()
            ->validate(
                $value,
                new Assert\Collection(
                    fields:[
                        'url' => new Assert\Url()
                    ],
                    allowExtraFields: false,
                    extraFieldsMessage: 'Le champs {{ field }} n\'est pas valide.',
                    missingFieldsMessage: 'Il manque le champs {{ field }}.',
                )
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
                ->atPath($error->getPropertyPath())
                ->addViolation();
        }
    }
}