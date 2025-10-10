<?php

namespace App\EventListener\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Role;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LoginSuccessEvent::class, method: 'onSuccess', priority: -1)]
#[AsEventListener(event: LogoutEvent::class, method: 'onLogout')]
class AuthenticationListener
{
    private const AUTH_COOKIE = 'AUTH_TOKEN';

    public function __construct(
        private UserRepository $userRepository,
        private TokenStorageInterface $tokenStorage,
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

        $userChangeRole = false;

        if ($user->isInvited() && $user->hasRole(Role::GUEST)) {
            $userChangeRole = true;
            $user->setRoles([Role::USER]);
            $user->setIsVerified(true);
        }

        $this->userRepository->update($user);

        if ($userChangeRole) {
            $this->tokenStorage->setToken(
                new UsernamePasswordToken(
                    $user,
                    'main',
                    $user->getRoles()
                )
            );
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) {
            return;
        }

        $response->headers->clearCookie(self::AUTH_COOKIE);
    }
}
