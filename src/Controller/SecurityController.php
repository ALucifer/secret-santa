<?php

namespace App\Controller;

use App\Attributes\AnonymousUser;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RegisterType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\Role;
use LogicException;
use Psr\Log\LoggerInterface;
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

    #[Route('/email/verify', name: 'app_security_verifyemail')]
    public function verifyEmail(
        Request $request,
        TokenRepository $tokenRepository,
        UserRepository $userRepository,
        LoggerInterface $logger,
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
            $logger->error($e->getMessage());
            return new Response('test', Response::HTTP_UNAUTHORIZED);
        }

        return $this->render('security/verify-email.html.twig');
    }

    #[Route('/members/change-password', name: 'app_members_change_password')]
    public function verifyMember(
        Request $request,
        Security $security,
        UserRepository $userRepository
    ): ?Response {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);

        $user = $security->getUser();

        if ($form->isSubmitted() && $form->isValid() && $user && $user instanceof User) {
            /** @var array{ password: string } $data */
            $data = $form->getData();

            $user = $userRepository->verifyMember($user, $data);

            return $security->login($user, 'login_link');
        }

        return $this->render(
            'security/member-change-password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(): Response {
        return $this->render('security/forgot-password.html.twig');
    }
}