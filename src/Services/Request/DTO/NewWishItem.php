<?php

namespace App\Services\Request\DTO;

use App\Entity\WishitemType;
use App\Services\Request\DTO\Wish\WishDTOInterface;

readonly class NewWishItem
{
    public function __construct(
        public readonly WishitemType $type,
        public readonly WishDTOInterface $data,
    ) {
    }
}