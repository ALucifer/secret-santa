<?php

namespace App\EventListener;

use App\Entity\User;
use App\Services\UserRequirements\UserRequirementsHandler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsEventListener(event: KernelEvents::CONTROLLER_ARGUMENTS, method: 'onKernelControllerArguments')]
class IsGrantedAttributeListener
{
    public function __construct(
        private Security $security,
        private UserRequirementsHandler $userRequirementsHandler,
        private RouterInterface $router,
    ) {
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $attributes = $event->getAttributes(IsGranted::class);

        if ([] === $attributes) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $requirements = $this->userRequirementsHandler->handle();

        if ($requirements->getRaw()) {
            $event->setController(
                fn () => new RedirectResponse(
                    $this->router->generate('profile_incomplete'),
                )
            );
        }
    }
}