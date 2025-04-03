<?php

namespace App\EventListener\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

#[AsEventListener(event: KernelEvents::RESPONSE, method: 'onKernelResponse')]
class CheckJWTTokenListener
{
    private const AUTH_COOKIE = 'AUTH_TOKEN';

    public function __construct(
        private Security $security,
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$event->isMainRequest() || !$user) {
            return;
        }

        try {
            $this->jwtManager->parse(
                $event->getRequest()->cookies->getString(self::AUTH_COOKIE)
            );
        } catch (Throwable $e) {
            $jwt = $this->jwtManager->create($user);

            $event->getResponse()->headers->setCookie(
                Cookie::create(self::AUTH_COOKIE)
                    ->withValue($jwt)
                    ->withSameSite('strict')
                    ->withHttpOnly(false)
            );
        }
    }
}