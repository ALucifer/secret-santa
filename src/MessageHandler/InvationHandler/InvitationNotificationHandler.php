<?php

namespace App\MessageHandler\InvationHandler;

use LogicException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InvitationNotificationHandler
{
    public function __invoke(InviationNotification $message): void
    {
        throw new LogicException('Not implemented');
    }
}