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
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

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
                $user = $userRepository->createUserFromInvitation($newMemberDTO);
            } catch (\Throwable $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
            }
        }

        $member = $secretSantaMemberRepository->create($secretSanta, $user);

        return $this->json(Member::fromMember($member));
    }

    #[Route('/api/secret-santa/{secretId}/delete/member/{secretSantaMember}', name: 'deleteMember', methods: ['DELETE', 'GET'])]
    public function deleteMember(
        SecretSanta $secretId,
        SecretSantaMember $secretSantaMember,
        SecretSantaMemberRepository $secretSantaMemberRepository,
        LoggerInterface $logger,
    ): JsonResponse {
        if ($secretId->getId() !== $secretSantaMember->getSecretSanta()->getId()) {
            return new JsonResponse(['error' => 'Member not found in this secret santa'], Response::HTTP_NOT_FOUND);
        }

        try {
            $secretSantaMemberRepository->delete($secretSantaMember);

            return new JsonResponse(null, Response::HTTP_OK);
        } catch (Throwable $e) {
            $logger->error($e->getMessage());
            return new JsonResponse(null, Response::HTTP_CONFLICT);
        }
    }

    #[Route('/api/secret-santa/{id}/members', name: 'members', methods: ['GET'])]
    public function members(SecretSanta $secretSanta): JsonResponse
    {
        $members = $secretSanta->getMembers()->toArray();
        Assertion::allIsInstanceOf($members, SecretSantaMember::class);

        return $this->json(Members::fromEntity($members));
    }
}