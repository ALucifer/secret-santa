<?php

namespace App\MessageHandler\SantaHandler;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(['async'])]
class Santa
{
    public function __construct(
        private int $memberId,
        private string $secretSantaId,
    ) {
    }

    public function memberId(): int
    {
        return $this->memberId;
    }

    public function secretSantaId(): string
    {
        return $this->secretSantaId;
    }
}