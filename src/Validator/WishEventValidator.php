<?php

namespace App\Validator;

use App\Validator\Constraints\WishEvent;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WishEventValidator extends ConstraintValidator
{
    /**
     * @param array{ date: string, name: string } $value
     * @param Constraint $constraint
     * @return void
     * @throws \Exception
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof WishEvent) {
            throw new UnexpectedTypeException($constraint, WishEvent::class);
        }

        $errors = $this
            ->context
            ->getValidator()
            ->validate(
                $value,
                new Assert\Collection(
                    fields: [
                        'name' => [
                            new Assert\Type('string'),
                            new Assert\NotBlank(),
                            new Assert\Length(min: 3, max: 50),
                        ],
                        'date' => new Assert\Date(),
                    ],
                    allowExtraFields: false,
                    extraFieldsMessage: 'Le champs {{ field }} n\'est pas valide.',
                    missingFieldsMessage: 'Il manque le champs {{ field }}.',
                ),
            );

        if ($errors->count() > 0) {
            $this->handleErrors($errors);
        }

        $dateError = $this
            ->context
            ->getValidator()
            ->validate(
                new DateTimeImmutable($value['date']),
                new Assert\GreaterThan('today'),
            );

        if ($dateError->count() > 0) {
            $this->handleErrors($dateError, '[date]');
        }
    }

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
