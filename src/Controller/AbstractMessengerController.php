<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractMessengerController extends AbstractController
{
    public function __construct(
        protected MessageBusInterface $messageBus,
        protected LoggerInterface $logger,
    ) {
    }
}
