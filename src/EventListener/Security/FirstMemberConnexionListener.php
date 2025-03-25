<?php

namespace App\EventListener\Security;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'onKernelException')]
class FirstMemberConnexionListener
{
    public function __construct(
        private Security $security,
        private RouterInterface $router,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            /**
             * @var User $user
             */
            $user = $this->security->getUser();

            if ($user->isInvited() && !$user->isVerified()) {
                $event->setResponse(
                    new RedirectResponse($this->router->generate('app_members_change_password'))
                );
            }

        }
    }
}