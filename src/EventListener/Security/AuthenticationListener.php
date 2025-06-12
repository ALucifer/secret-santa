<?php

namespace App\EventListener\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LoginSuccessEvent::class, method: 'onSuccess', priority: -1)]
#[AsEventListener(event: LoginSuccessEvent::class, method: 'onFirstLogInMemberSuccess', priority: 10)]
#[AsEventListener(event: LogoutEvent::class, method: 'onLogout')]
#[AsEventListener(event: KernelEvents::RESPONSE, method: 'onKernelResponse')]
class AuthenticationListener
{
    private const AUTH_COOKIE = 'AUTH_TOKEN';

    public function __construct(
        private RouterInterface $router,
        private UserRepository $userRepository,
        private Security $security,
    ) {
    }

    public function onSuccess(LoginSuccessEvent $event): void
    {
        $response = $event->getResponse();

        if ('main' !== $event->getFirewallName() || !$response) {
            return;
        }
        /** @var User $user */
        $user = $event->getUser();
        $user->setLastActivity(new \DateTimeImmutable());

        $this->userRepository->update($user);
    }

    public function onFirstLogInMemberSuccess(LoginSuccessEvent $event): void
    {
//        $response = $event->getResponse();
//
//        if ('main' !== $event->getFirewallName() || !$response) {
//            return;
//        }
//
//        /** @var User $user */
//        $user = $event->getUser();
//
//        if (!$user->isVerified() && $user->isInvited()) {
//            $redirectResponse = new RedirectResponse($this->router->generate('test'));
//            $event->setResponse($redirectResponse);
//
//            $event->stopPropagation();
//        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) return;

        $response->headers->clearCookie(self::AUTH_COOKIE);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$event->isMainRequest() || !$user) {
            return;
        }

        if (!$user instanceof User) {
            return;
        }

        $routeName = $event->getRequest()->attributes->get('_route');

        if (str_contains($routeName, 'test')) {
            return;
        }

        if ($user->isInvited() && !$user->isVerified()) {
            $event->setResponse(new RedirectResponse($this->router->generate('test')));

            $event->stopPropagation();
        }

        if (!$user->getPseudo()) {
//            $event->setResponse(new RedirectResponse($this->router->generate('')));
//
//            $event->stopPropagation();
        }
    }
}