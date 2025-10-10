<?php

namespace App\Services\Request\DTO;

use App\Entity\WishitemType;
use App\Services\Request\DTO\Wish\WishDTOInterface;

readonly class NewWishItem
{
    public function __construct(
        public WishitemType $type,
        public WishDTOInterface $data,
    ) {
    }
}
