<?php

namespace App\Controller;

use App\Entity\DTO\Members;
use App\Entity\SecretSanta;
use App\Entity\SecretSantaMember;
use App\Entity\User;
use App\Form\SecretSantaType;
use App\Repository\SecretSantaMemberRepository;
use App\Repository\SecretSantaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[IsGranted('ROLE_USER')]
class SecretSantaController extends AbstractController
{
    #[Route('/secret-santa/new', name: 'secret_santa')]
    public function create(
        Request $request,
        Security $security,
        SecretSantaRepository $secretSantaRepository,
        SecretSantaMemberRepository $secretSantaMemberRepository,
    ): Response {
        $form = $this->createForm(SecretSantaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SecretSanta $secretSanta */
            $secretSanta = $form->getData();

            $user = $security->getUser();

            if (!$user instanceof User) {
                throw new LogicException(sprintf('User must be an instance of %s.', User::class));
            }

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
            'secret-santa/create.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }

    #[Route('/secret-santa/{id}', name: 'secret_santa_view')]
    public function view(
        SecretSanta $secretSanta,
    ): Response {
        return $this->render(
            'secret-santa/view.html.twig',
            [
                'secretSanta' => $secretSanta,
                'members' => Members::fromEntity($secretSanta->getMembers()->toArray()),
            ],
        );
    }

    #[Route(
        path: '/secret-santa/{secretSanta}/members/{secretSantaMember}/wishlist',
        name: 'secret_santa_member_wishlist',
        options: ['expose' => true],
        host: '%app.host%'
    )]
    public function memberList(
        SecretSanta $secretSanta,
        SecretSantaMember $secretSantaMember,
        NormalizerInterface $normalizer
    ): Response {
        return $this->render(
            'secret-santa/member-wishlist.html.twig',
            [
                'member' => $secretSantaMember,
                'secretSanta' => $secretSanta,
                'whishItems' => $normalizer->normalize($secretSantaMember->getWishItems()->toArray(), 'json', [ 'groups' => 'default'])
            ]
        );
    }
}