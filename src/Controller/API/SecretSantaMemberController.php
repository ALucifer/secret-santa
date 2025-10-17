<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Member as EntityMember;
use App\Entity\SecretSanta;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use App\Services\Factory\EntityDto\MemberFactoryInterface;
use App\Services\Request\DTO\NewMemberDTO;
use Assert\Assertion;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route(path: '/api')]
final class SecretSantaMemberController extends AbstractController
{
    #[Route(
        path: '/secret-santa/{id}/register/member',
        name: 'registerMember',
        options: ['expose' => true],
        methods: ['POST'],
    )]
    #[IsGranted('ADD_MEMBER', 'secretSanta')]
    public function registerFromSecret(
        SecretSanta $secretSanta,
        #[MapRequestPayload(
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
        )] NewMemberDTO $newMemberDTO,
        UserRepository $userRepository,
        MemberRepository $secretSantaMemberRepository,
        MemberFactoryInterface $memberFactory,
    ): JsonResponse {
        $user = $userRepository->findOneBy(['email' => $newMemberDTO->email]);

        if (!$user) {
            try {
                $user = $userRepository->createUserFromInvitation($newMemberDTO);
            } catch (\Throwable $e) {
                return $this->json($e->getMessage(), Response::HTTP_NOT_FOUND);
            }
        }

        $member = $secretSantaMemberRepository->create($secretSanta, $user);

        return $this->json($memberFactory->build($member));
    }

    #[Route(
        path: '/secret-santa/{secretSanta}/delete/member/{member}',
        name: 'deleteMember',
        options: ['expose' => true],
        methods: ['DELETE'],
    )]
    public function deleteMember(
        SecretSanta $secretSanta,
        EntityMember $member,
        MemberRepository $secretSantaMemberRepository,
        LoggerInterface $logger,
    ): JsonResponse {
        if ($secretSanta->getId() !== $member->getSecretSanta()->getId()) {
            return $this->json(['error' => 'Member not found in this secret santa'], Response::HTTP_NOT_FOUND);
        }

        try {
            $secretSantaMemberRepository->delete($member);

            return $this->json(null, Response::HTTP_OK);
        } catch (\Throwable $e) {
            $logger->error($e->getMessage());
            return $this->json(null, Response::HTTP_CONFLICT);
        }
    }

    #[Route(
        path: '/secret-santa/{id}/members',
        name: 'members',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function members(SecretSanta $secretSanta, MemberFactoryInterface $memberFactory): JsonResponse
    {
        $members = $secretSanta->getMembers()->toArray();
        Assertion::allIsInstanceOf($members, EntityMember::class);

        return $this->json($memberFactory->buildCollection($members));
    }
}
