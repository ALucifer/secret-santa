<?php

namespace App\EventListener\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LoginSuccessEvent::class, method: 'onSuccess', priority: -1)]
#[AsEventListener(event: LoginSuccessEvent::class, method: 'onFirstLogInMemberSuccess', priority: 10)]
#[AsEventListener(event: LogoutEvent::class, method: 'onLogout')]
class AuthenticationListener
{
    private const AUTH_COOKIE = 'AUTH_TOKEN';

    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RouterInterface $router,
    ) {
    }

    public function onSuccess(LoginSuccessEvent $event): void
    {
        $response = $event->getResponse();

        if ('main' !== $event->getFirewallName() || !$response) {
            return;
        }

        $jwt = $this->jwtManager->create($event->getUser());

        $redirectResponse = new RedirectResponse($this->router->generate('user_profile'));

        $redirectResponse
            ->headers
            ->setCookie(
                Cookie::create(self::AUTH_COOKIE)
                    ->withValue($jwt)
                    ->withSameSite('strict')
                    ->withHttpOnly(false)
            );

        $event->setResponse($redirectResponse);
    }

    public function onFirstLogInMemberSuccess(LoginSuccessEvent $event): void
    {
        $response = $event->getResponse();

        if ('main' !== $event->getFirewallName() || !$response) {
            return;
        }

        /** @var User $user */
        $user = $event->getUser();

        if (!$user->isVerified() && $user->isInvited()) {
            $redirectResponse = new RedirectResponse($this->router->generate('app_members_change_password'));
            $event->setResponse($redirectResponse);

            $event->stopPropagation();
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) return;

        $response->headers->clearCookie(self::AUTH_COOKIE);
    }
}