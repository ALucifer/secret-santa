<?php

namespace App\Controller;

use App\Attributes\AnonymousUser;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\Role;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    #[AnonymousUser(redirectRouteName: 'user_profile')]
    public function register(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(RegisterType::class, new User());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setRoles([Role::USER]);
            $userRepository->create($user);

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/login', name: 'app_login')]
    #[AnonymousUser(redirectRouteName: 'user_profile')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        if ($security->getUser()) {
            return $this->redirectToRoute('user_profile');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'error' => $error,
                'last_username' => $lastUsername,
            ]
        );
    }

    #[Route('/login_check', name: 'login_check')]
    public function check(): never
    {
        throw new LogicException('This code should never be reached');
    }

    #[Route('/email/verify/{token}', name: 'app_security_verifyemail')]
    public function verifyEmail(
        string $token,
        TokenRepository $tokenRepository,
        UserRepository $userRepository,
    ): Response {
        try {
            $token = $tokenRepository->findOneByOrFail(['token' => $token]);

            if (!$token->isValid()) {
                $this->addFlash('error', 'Lien invalide.');
                return $this->redirectToRoute('app_login');
            }

            $user = $userRepository->findOneByOrFail(['id' => $token->getUser()->getId()]);
            $user->setIsVerified(true);

            $userRepository->update($user);
            $tokenRepository->invalidToken($token);

            $this->addFlash('success', 'Votre email à bien été vérifié, vous pouvez maintenant vous connecter.');
        } catch (NotFoundHttpException $e) {
            $this->addFlash('error', 'Lien invalide.');
        }

        return $this->redirectToRoute('app_login');
    }
}
