<?php

namespace App\Controller;

use App\Entity\DTO\Members;
use App\Entity\SecretSanta;
use App\Entity\SecretSantaMember;
use App\MessageHandler\SantaHandler\Santa;
use App\Repository\SecretSantaMemberRepository;
use App\Repository\SecretSantaRepository;
use App\Services\RandomizeCollection;
use Random\Randomizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

#[IsGranted('ROLE_USER')]
class SecretSantaController extends AbstractController
{
    #[Route('/secret-santa/{id}', name: 'secret_santa_view', options: ['expose' => true])]
    #[IsGranted('SHOW', 'secretSanta')]
    public function view(
        SecretSanta $secretSanta,
    ): Response {
        $members = $secretSanta
            ->getMembers()
            ->filter(
                function (SecretSantaMember $member) use ($secretSanta) {
                    if ($secretSanta->getState() !== 'started') {
                        return true;
                    }

                    return $member->getState() === 'approved';
                });

        return $this->render(
            'secret-santa/view.html.twig',
            [
                'secretSanta' => $secretSanta,
                'members' => Members::fromEntity($members->getValues()),
            ],
        );
    }

    #[Route(
        path: '/secret-santa/{secretSanta}/members/{secretSantaMember}/wishlist',
        name: 'secret_santa_member_wishlist',
        options: ['expose' => true],
        host: '%app.host%'
    )]
    #[IsGranted('SHOW', 'secretSantaMember')]
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
                'whishItems' => $normalizer->normalize($secretSantaMember->getWishItems()->toArray(), 'json', ['groups' => 'default'])
            ]
        );
    }

    #[Route(
        path: '/secret-santa/{id}/start',
        name: 'secret_santa_start',
        methods: ['POST', 'GET'],
    )]
    #[IsGranted('START', 'secretSanta')]
    public function start(
        SecretSanta $secretSanta,
        WorkflowInterface $secret_workflow,
        SecretSantaRepository $secretSantaRepository,
        SecretSantaMemberRepository $secretSantaMemberRepository,
        RandomizeCollection $randomizeCollection,
        MessageBusInterface $messageBus
    ): Response {
        try {
            $secret_workflow->apply($secretSanta, 'to_started');

            $members = $secretSanta->getMembers()->filter(function (SecretSantaMember $member) { return $member->getState() === 'approved'; })->getValues();
            $shuffled = $randomizeCollection->randomizeCollection($members);

            foreach ($members as $key => $member) {
                $member->setSanta($shuffled[$key]);
                $secretSantaMemberRepository->update($member);

                $messageBus->dispatch(
                    new Santa(
                        $member->getId() ?? throw new LogicException('Member id cannot be null.'),
                        $secretSanta->getId() ?? throw new LogicException('Secret santa id cannot be null.'),
                    )
                );
            }

            $secretSantaRepository->update($secretSanta);
            return $this->redirectToRoute('secret_santa_view', ['id' => $secretSanta->getId()]);
        } catch (LogicException $e) {
            return $this->redirectToRoute('secret_santa_view', ['id' => $secretSanta->getId()]);
        }
    }
}