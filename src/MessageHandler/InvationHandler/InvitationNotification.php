<?php

namespace App\MessageHandler\InvationHandler;

use App\Entity\SecretSanta;
use App\Entity\User;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(['async'])]
class InvitationNotification
{
    public function __construct(
        private User $user,
        private SecretSanta $secretSanta,
    ) {
    }

    public function user(): User
    {
        return $this->user;
    }

    public function secretSanta(): SecretSanta
    {
        return $this->secretSanta;
    }
}
