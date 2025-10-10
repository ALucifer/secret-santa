<?php

namespace App\MessageHandler\RegisterNotification;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(['async'])]
class RegisterNotification
{
    public function __construct(
        private int $userId,
    ) {
    }

    public function userId(): int
    {
        return $this->userId;
    }
}
