<?php

declare(strict_types=1);

namespace App\Services\Factory\EntityDto;

use App\Entity\DTO\WishItem\WishItem as WishItemDto;
use App\Entity\Wishitem;

interface WishItemFactoryInterface
{
    public function build(Wishitem $wishItem): WishItemDto;

    /**
     * @param array<Wishitem> $wishItems
     * @return array<WishItemDto>
     */
    public function buildCollection(array $wishItems): array;
}