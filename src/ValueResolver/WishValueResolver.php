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
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTaggedItem(index: 'wish_value', priority: 150)]
class WishValueResolver implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable<NewWishItem>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if ($type !== NewWishItem::class) {
            return [];
        }

        /**
         * @var array{ type: string, data: array<Event|Gift|Money> } $jsonData
         */
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

    /**
     * @param array<Event|Gift|Money> $data
     * @param Constraint $constraint
     * @param string $type
     * @return WishDTOInterface
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function handleWish(array $data, Constraint $constraint, string $type): WishDTOInterface
    {
        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            throw new ValidationFailedException($data, $violations);
        }

        $object = $this->denormalizer->denormalize($data, $type);

        if (!$object instanceof WishDTOInterface) {
            throw new LogicException('Normalizer should return WishDTOInterface object');
        }

        return $object;
    }
}