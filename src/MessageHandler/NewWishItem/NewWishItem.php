<?php

namespace App\MessageHandler\NewWishItem;

use App\Entity\WishitemMember;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(['async'])]
class NewWishItem
{
    public function __construct(
        private WishitemMember $wishItem
    ) {
    }

    public function wishItem(): WishitemMember
    {
        return $this->wishItem;
    }
}