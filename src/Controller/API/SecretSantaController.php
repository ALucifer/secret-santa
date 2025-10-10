<?php

namespace App\Controller\API;

use App\Entity\Member as EntityMember;
use App\Entity\SecretSanta;
use App\Entity\User;
use App\Repository\SecretSantaRepository;
use App\Services\Factory\EntityDto\SecretSantaFactoryInterface;
use App\Services\Request\DTO\NewSecretSantaDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route(path: '/api')]
class SecretSantaController extends AbstractController
{
    #[Route(
        path: '/secret-santa',
        name: 'newSecret',
        options: ['expose' => true],
        methods: ['POST'],
    )]
    public function newSecret(
        #[MapRequestPayload] NewSecretSantaDTO $secretSantaDTO,
        SecretSantaRepository $secretSantaRepository,
        SecretSantaFactoryInterface $secretSantaFactory,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('Vous ne pouvez pas avoir accès.');
        }

        $secretSanta = new SecretSanta();

        $secretSanta
            ->setOwner($user)
            ->setLabel($secretSantaDTO->label);


        if ($secretSantaDTO->registerMe) {
            $member = new EntityMember();
            $member
                ->setSecretSanta($secretSanta)
                ->setUser($user);

            $secretSanta->addMember($member);
        }

        $secretSanta = $secretSantaRepository->create($secretSanta);

        return $this->json(
            data: $secretSantaFactory->build($secretSanta),
        );
    }
}
