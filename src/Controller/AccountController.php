<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ForgotPasswordType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\TokenExtractor;
use App\Services\Mailer\ForgotPasswordMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/change-password', name: 'app_members_change_password')]
    public function verifyMember(
        Request $request,
        TokenExtractor $tokenExtractor,
        UserRepository $userRepository,
    ): ?Response {

        if (!$tokenExtractor->isValid()) {
            $this->addFlash('error', 'LIEN INVALIDE.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{ password: string } $data */
            $data = $form->getData();

            $user = $tokenExtractor->getToken()->getUser();

            $userRepository->upgradePassword($user, $data['password']);

            $this->addFlash('success', 'mot de passe modifié avec succès.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/member-change-password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request,
        UserRepository $userRepository,
        ForgotPasswordMailer $mailer,
        TokenRepository $tokenRepository,
    ): Response {
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $userRepository->findOneBy(['email' => $data['email']]);

            if ($user) {
                $token = $tokenRepository->createForgotPasswordToken($user);

                $mailer->send($user, $token);
            }

            $this->addFlash('success', 'Un email de changement de mot de passe vient d\'être envoyé.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/forgot-password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
