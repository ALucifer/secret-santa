<?php

declare(strict_types=1);

namespace App\Services\Factory\EntityDto;

use App\Entity\DTO\WishItem\WishItem as WishItemDto;
use App\Entity\Wishitem;

final class WishItemFactory implements WishItemFactoryInterface
{
    public function build(Wishitem $wishItem): WishItemDto
    {
        return new WishItemDto(
            id: $wishItem->getId() ?? throw new \LogicException('Wish item cannot be created without an ID.'),
            type: $wishItem->getType()->value,
            data: $wishItem->getData(),
        );
    }

    public function buildCollection(array $wishItems): array
    {
        return array_map(fn (Wishitem $wishItem) => $this->build($wishItem), $wishItems);
    }
}
