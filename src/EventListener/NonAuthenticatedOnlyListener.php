<?php

namespace App\EventListener;

use App\Attributes\AnonymousUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

#[AsEventListener(event: KernelEvents::CONTROLLER_ARGUMENTS, method: 'onKernelControllerArguments')]
class NonAuthenticatedOnlyListener
{
    public function __construct(
        private Security $security,
        private RouterInterface $router,
    ) {
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event)
    {
        $attributes = $event->getAttributes(AnonymousUser::class);

        if ($attributes === []) {
            return;
        }

        /** @var AnonymousUser $attribute */
        $attribute = $attributes[0];

        if ($this->security->getUser()) {
            $event->setController(
                fn () => new RedirectResponse(
                    $this->router->generate($attribute->redirectRouteName)
                )
            );
        }
    }
}
