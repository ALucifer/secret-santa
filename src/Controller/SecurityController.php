<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(RegisterType::class, new User());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setRoles([Role::ROLE_USER]);
            $userRepository->create($user);
        }

        return $this->render(
            'security/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
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

    #[Route('/email/verify', name: 'app_security_verifyemail')]
    public function verifyEmail(
        Request $request,
        TokenRepository $tokenRepository,
        UserRepository $userRepository
    ): Response {
        try {
            $token = $tokenRepository->findOneByOrFail(['token' => $request->get('token')]);

            if (!$token->isValid()) {
                $this->addFlash('error', 'Invalid token');
                return $this->redirectToRoute('home');
            }

            $user = $userRepository->findOneByOrFail(['id' => $token->getUser()->getId()]);
            $user->setIsVerified(true);

            $userRepository->update($user);
            $tokenRepository->invalidToken($token);
        } catch (NotFoundHttpException $e) {
            dd($e->getMessage());
        }
        return $this->render('security/verify-email.html.twig');
    }
}