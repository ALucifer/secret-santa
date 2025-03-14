<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SecretSantaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(SecretSantaRepository $secretSantaRepository, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user && !$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }


        return $this->render(
            'user/profile.html.twig',
            [
                'items' => $secretSantaRepository->findUserSecretsSanta($user),
            ],
        );
    }
}