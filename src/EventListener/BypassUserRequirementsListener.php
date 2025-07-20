<?php

namespace App\EventListener;

use App\Attributes\BypassUserRequirements;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::CONTROLLER_ARGUMENTS, method: 'onKernelControllerArguments')]
class BypassUserRequirementsListener
{
    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $attributes = $event->getAttributes(BypassUserRequirements::class);

        if ($attributes === []) {
            return;
        }

        $attribute = $attributes[0];

        $event->stopPropagation();
    }
}