<?php

namespace App\EventListener\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LoginSuccessEvent::class, method: 'onSuccess')]
#[AsEventListener(event: LogoutEvent::class, method: 'onLogout')]
class AuthenticationListener
{
    private const AUTH_COOKIE = 'AUTH_TOKEN';

    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function onSuccess(LoginSuccessEvent $event): void
    {
        $response = $event->getResponse();

        if ('main' !== $event->getFirewallName() || !$response) {
            return;
        }

        $jwt = $this->jwtManager->create($event->getUser());

        $response
            ->headers
            ->setCookie(
                Cookie::create(self::AUTH_COOKIE)
                    ->withValue($jwt)
                    ->withSameSite('strict')
                    ->withHttpOnly(false)
            );
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) return;

        $response->headers->clearCookie(self::AUTH_COOKIE);
    }
}