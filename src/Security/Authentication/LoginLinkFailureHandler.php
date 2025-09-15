<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

final class LoginLinkFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        private RouterInterface $router,
        private UserRepository $userRepository,
    ) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $user = $request->query->has('user') ? $this->userRepository->findOneBy(['email' => $request->query->get('user')]) : null;

        $url = ($user?->isVerified()) ? $this->router->generate('app_login') : $this->router->generate('app_forgot_password');
        $message = ($user?->isVerified()) ? 'Lien expiré, veuillez vous connecter manuellement.' : 'Lien expiré, veuillez demander un nouveau mot de passe.';

        // Voir les différents cas
        $session = $request->getSession();
        $session->getFlashBag()->add('error', $message);
        return new RedirectResponse($url);
    }
}