<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\SecretSanta;
use App\MessageHandler\SantaHandler\Santa;
use App\Repository\MemberRepository;
use App\Repository\SecretSantaRepository;
use App\Services\Factory\EntityDto\MemberFactoryInterface;
use App\Services\Factory\EntityDto\SecretSantaFactoryInterface;
use App\Services\RandomizeCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

#[IsGranted('ROLE_USER')]
class SecretSantaController extends AbstractController
{
    public function __construct(
        private readonly SecretSantaFactoryInterface $secretSantaFactory,
        private readonly MemberFactoryInterface $memberFactory,
    ) {
    }

    #[Route('/secret-santa/{id}', name: 'secret_santa_view', options: ['expose' => true])]
    #[IsGranted('SHOW', 'secretSanta')]
    public function view(
        SecretSanta $secretSanta,
    ): Response {
        $members = $secretSanta
            ->getMembers()
            ->filter(
                function (Member $member) use ($secretSanta) {
                    if ($secretSanta->getState() !== 'started') {
                        return true;
                    }

                    return $member->getState() === 'approved';
                }
            );

        return $this->render(
            'secret-santa/view.html.twig',
            [
                'secretSanta' => $this->secretSantaFactory->build($secretSanta),
                'members' => $this->memberFactory->buildCollection($members->getValues()),
            ],
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
        WorkflowInterface $secretWorkflow,
        SecretSantaRepository $secretSantaRepository,
        MemberRepository $secretSantaMemberRepository,
        RandomizeCollection $randomizeCollection,
        MessageBusInterface $messageBus
    ): Response {
        try {
            $secretWorkflow->apply($secretSanta, 'to_started');

            $members = $secretSanta
                ->getMembers()
                ->filter(
                    function (Member $member) {
                        return $member->getState() === 'approved';
                    }
                )
                ->getValues();
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
