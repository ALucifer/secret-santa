<?php

declare(strict_types=1);

namespace App\Entity\DTO\WishItem;

final readonly class WishItem
{
    public function __construct(
        public int $id,
        public string $type,
        public array $data,
    ) {
    }
}