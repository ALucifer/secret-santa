<?php

namespace App\MessageHandler\NewWishItem;

use Symfony\Component\Messenger\Attribute\AsMessage;
use App\Services\Request\DTO\NewWishItem as NewWishItemDTO;

#[AsMessage(['alexis'])]
class NewWishItem
{
    public function __construct(
        private NewWishItemDTO $wishItem,
        private int $memberId,
        private int $taskId,
    ) {
    }

    public function wishItem(): NewWishItemDTO
    {
        return $this->wishItem;
    }

    public function memberId(): int
    {
        return $this->memberId;
    }

    public function taskId(): int
    {
        return $this->taskId;
    }
}
