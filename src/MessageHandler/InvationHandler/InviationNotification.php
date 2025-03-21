<?php

namespace App\MessageHandler\InvationHandler;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(['async'])]
class InviationNotification
{

}