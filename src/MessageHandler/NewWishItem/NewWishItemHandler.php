<?php

namespace App\MessageHandler\NewWishItem;

use App\Entity\WishitemType;
use App\Repository\WishitemMemberRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewWishItemHandler
{
    public function __construct(
      private WishitemMemberRepository $repository,
    ) {
    }

    public function __invoke(
        NewWishItem $wishItem,
    )
    {
        match ($wishItem->wishItem()->getType()) {
            WishitemType::GIFT => throw new \LogicException('Not implemented.'),
            default => $this->repository->save($wishItem->wishItem()),
        };
    }
}