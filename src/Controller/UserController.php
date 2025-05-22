<?php

namespace App\Controller;

use App\Entity\SecretSanta;
use App\Entity\User;
use App\Form\SecretSantaType;
use App\Repository\SecretSantaMemberRepository;
use App\Repository\SecretSantaRepository;
use App\Services\Request\Attribute\PrefixPagination;
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
        Request $request,
        #[PrefixPagination(prefix: 'user_')] PaginationDTO $paginationUserDTO,
        #[PrefixPagination(prefix: 'inv_')] PaginationDTO $paginationInvitedDTO,
        SecretSantaRepository $secretSantaRepository,
        SecretSantaMemberRepository $secretSantaMemberRepository,
        Security $security
    ): Response {
        $user = $security->getUser();

        if (!$user || !$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(SecretSantaType::class);
        $form->handleRequest($request);

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
                'invitedSecretSantas' => $secretSantaRepository->findInvitedUserInSecretSanta($user, $paginationInvitedDTO),
                'userItems' => $secretSantaRepository->findPaginatedUserSecretsSanta($user, $paginationUserDTO),
                'form' => $form->createView(),
            ],
        );
    }
}