<?php

namespace App\Controller;

use App\Attributes\BypassUserRequirements;
use App\Attributes\PrefixPagination;
use App\Entity\SecretSanta;
use App\Entity\User;
use App\Form\SecretSantaType;
use App\Form\UserRequirementsType;
use App\Repository\MemberRepository;
use App\Repository\SecretSantaRepository;
use App\Repository\UserRepository;
use App\Services\Request\DTO\PaginationDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(
        Request                                            $request,
        #[PrefixPagination(prefix: 'user_')] PaginationDTO $paginationUserDTO,
        #[PrefixPagination(prefix: 'inv_')] PaginationDTO  $paginationInvitedDTO,
        SecretSantaRepository                              $secretSantaRepository,
        MemberRepository                                   $secretSantaMemberRepository,
        Security                                           $security
    ): Response {
        $user = $security->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(SecretSantaType::class);
        $form->handleRequest($request);

        // @todo: a supprimer, plus utilisé
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SecretSanta $secretSanta */
            $secretSanta = $form->getData();

            $secretSanta->setOwner($user);

            $secretSanta = $secretSantaRepository->create($secretSanta);

            if ($form['registerMe'] && $form['registerMe']->getData()) {
                $secretSantaMemberRepository->addMember($secretSanta, $user);
            }

            return $this->redirectToRoute(
                'secret_santa_view',
                [
                    'id' => $secretSanta->getId()
                ]
            );
        }

        return $this->render(
            'user/profile.html.twig',
            [
                'userItems' => $secretSantaRepository->findPaginatedUserSecretsSanta($user, $paginationUserDTO),
                'invitedSecretSantas' => $secretSantaRepository->findPaginatedUserInvitedInSecretSanta($user, $paginationInvitedDTO),
                'form' => $form->createView(),
            ],
        );
    }

    #[Route('/profile/complete', name: 'profile_incomplete')]
    #[IsGranted('ROLE_USER')]
    #[BypassUserRequirements]
    public function incompletProfile(Request $request, UserRepository $userRepository): Response
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