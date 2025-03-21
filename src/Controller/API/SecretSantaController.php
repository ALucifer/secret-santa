<?php

namespace App\Controller\API;

use App\Entity\DTO\Member;
use App\Entity\DTO\Members;
use App\Entity\SecretSanta;
use App\Entity\SecretSantaMember;
use App\Repository\SecretSantaMemberRepository;
use App\Repository\UserRepository;
use App\Services\Request\NewMemberDTO;
use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
class SecretSantaController extends AbstractController
{
    #[Route('/api/secret-santa/{id}/register/member', name: 'registerMember', methods: ['POST'])]
    public function registerFromSecret(
        SecretSanta $secretSanta,
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_NOT_FOUND)] NewMemberDTO $newMemberDTO,
        UserRepository $userRepository,
        SecretSantaMemberRepository $secretSantaMemberRepository,
    ): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $newMemberDTO->email]);

        if(!$user){
            try {
                $user = $userRepository->createMemberFromInvitation($newMemberDTO);
            } catch (\Throwable $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
            }
        }

        $member = $secretSantaMemberRepository->create($secretSanta, $user);

        return $this->json(Member::fromMember($member));
    }

    #[Route('/api/secret-santa/{id}/members', name: 'members', methods: ['GET'])]
    public function members(SecretSanta $secretSanta): JsonResponse
    {
        $members = $secretSanta->getMembers()->toArray();
        Assertion::allIsInstanceOf($members, SecretSantaMember::class);

        return $this->json(Members::fromEntity($members));
    }
}