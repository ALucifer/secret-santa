<?php

namespace App\Services\Request\DTO\Wish;

readonly class Money implements WishDTOInterface
{
    public function __construct(
        public int $price
    ) {
    }

    public function toArray(): array
    {
        return [
            'price' => $this->price,
        ];
    }
}