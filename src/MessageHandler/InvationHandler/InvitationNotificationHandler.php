<?php

namespace App\MessageHandler\InvationHandler;

use App\Services\Mailer\NewMemberMailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InvitationNotificationHandler
{
    public function __construct(
        private NewMemberMailer $mailer,
    ) {
    }

    public function __invoke(InvitationNotification $message): void
    {
        $this->mailer->sendInvitation($message->user(), $message->secretSanta());
    }
}