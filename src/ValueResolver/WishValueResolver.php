<?php

namespace App\ValueResolver;

use App\Entity\WishitemType;
use App\Services\Request\DTO\NewWishItem;
use App\Services\Request\DTO\Wish\Event;
use App\Services\Request\DTO\Wish\Gift;
use App\Services\Request\DTO\Wish\Money;
use App\Services\Request\DTO\Wish\WishDTOInterface;
use App\Validator\Constraints\WishEvent;
use App\Validator\Constraints\WishGift;
use App\Validator\Constraints\WishMoney;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WishValueResolver implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if ($type !== NewWishItem::class) {
            return [];
        }

        $jsonData = json_decode($request->getContent(), true);

        $object = match (strtoupper($jsonData['type'])) {
            WishitemType::MONEY->value => $this->handleWish($jsonData['data'], new WishMoney(), Money::class),
            WishitemType::EVENT->value => $this->handleWish($jsonData['data'], new WishEvent(), Event::class),
            WishitemType::GIFT->value => $this->handleWish($jsonData['data'], new WishGift(), Gift::class),
            default => throw new LogicException('Not implemented'),
        };

        $violations = $this->validator->validate($jsonData);

        if (count($violations) > 0) {
            throw new ValidationFailedException($jsonData, $violations);
        }

        $wishItem = new NewWishItem(
            WishitemType::from($jsonData['type']),
            $object
        );

        return [
            $wishItem
        ];
    }

    private function handleWish(array $data, Constraint $constraint, string $type): WishDTOInterface
    {
        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            throw new ValidationFailedException($data, $violations);
        }

        return $this->denormalizer->denormalize($data, $type);
    }
}