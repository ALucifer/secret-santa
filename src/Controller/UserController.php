<?php

namespace App\Controller;

use App\Attributes\BypassUserRequirements;
use App\Attributes\PrefixPagination;
use App\Entity\User;
use App\Form\UserRequirementsType;
use App\Repository\MemberRepository;
use App\Repository\SecretSantaRepository;
use App\Repository\UserRepository;
use App\Services\Request\DTO\PaginationDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private readonly SecretSantaRepository $secretSantaRepository,
    ) {
    }

    #[Route('/profile', name: 'user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(
        #[PrefixPagination(prefix: 'user_')] PaginationDTO $paginationUserDTO,
        #[PrefixPagination(prefix: 'inv_')] PaginationDTO $paginationInvitedDTO,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'user/profile.html.twig',
            [
                'userItems' =>
                    $this->secretSantaRepository->findPaginatedUserSecretsSanta($user, $paginationUserDTO),
                'invitedSecretSantas' =>
                    $this->secretSantaRepository->findPaginatedUserInvitedInSecretSanta($user, $paginationInvitedDTO),
            ],
        );
    }

    #[Route('/profile/complete', name: 'profile_incomplete')]
    #[IsGranted('ROLE_USER')]
    #[BypassUserRequirements]
    public function incompleteProfile(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserRequirementsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $data = $form->getData();

            if (!$user instanceof User) {
                throw new \LogicException('User must be authenticated.');
            }

            $user->setPseudo($data['pseudo'] ?? $user->getPseudo());
            $user->setPassword($data['password'] ?? $user->getPassword());

            $userRepository->updateAuthenticatedUser($user, isset($data['password']));

            return $this->redirectToRoute('user_profile');
        }

        return $this->render(
            'user/incomplet.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
