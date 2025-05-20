<?php

namespace App\Validator;

use App\Validator\Constraints\WishMoney;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


class WishMoneyValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof WishMoney) {
            throw new UnexpectedTypeException($constraint, WishMoney::class);
        }

        $errors = $this
            ->context
            ->getValidator()
            ->validate(
                $value,
                new Assert\Collection(
                    fields: [
                        'price' => [
                            new Assert\Type('integer'),
                            new Assert\GreaterThan(
                                value: 0,
                                message: 'The price must be greater than 0',
                            )
                        ],
                    ],
                    allowExtraFields: false,
                    extraFieldsMessage: 'Le champs {{ field }} n\'est pas valide.',
                    missingFieldsMessage: 'Il manque le champs {{ field }}.',
                ),
            );

        if ($errors->count() > 0) {
            $this->handleErrors($errors);
        }
    }

    /**
     * @param ConstraintViolationListInterface $errors
     * @param string|null $propertyPath
     * @return void
     */
    private function handleErrors(ConstraintViolationListInterface $errors, ?string $propertyPath = null): void
    {
        foreach ($errors as $error) {
            $this
                ->context
                ->buildViolation($error->getMessage())
                ->atPath($propertyPath ?? $error->getPropertyPath())
                ->addViolation();
        }
    }
}