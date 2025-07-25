<?php

namespace App\Controller\API;

use App\Entity\DTO\Member;
use App\Entity\DTO\Members;
use App\Entity\SecretSanta;
use App\Entity\Member as EntityMember;
use App\Entity\User;
use App\Form\SecretSantaType;
use App\Repository\SecretSantaMemberRepository;
use App\Repository\SecretSantaRepository;
use App\Repository\UserRepository;
use App\Services\Request\DTO\NewMemberDTO;
use App\Services\Request\DTO\NewSecretSantaDTO;
use Assert\Assertion;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[IsGranted("ROLE_USER")]
#[Route(path: '/api')]
class SecretSantaController extends AbstractController
{
    #[Route('/secret-santa/{id}/register/member', name: 'registerMember', methods: ['POST'])]
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

    #[Route('/secret-santa/{secretId}/delete/member/{secretSantaMember}', name: 'deleteMember', methods: ['DELETE', 'GET'])]
    public function deleteMember(
        SecretSanta $secretId,
        EntityMember $secretSantaMember,
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

    #[Route('/secret-santa/{id}/members', name: 'members', methods: ['GET'])]
    public function members(SecretSanta $secretSanta): JsonResponse
    {
        $members = $secretSanta->getMembers()->toArray();
        Assertion::allIsInstanceOf($members, Member::class);

        return $this->json(Members::fromEntity($members)); // @phpmd ignore StaticAccess
    }

    #[Route('/secret-santa', name: 'newSecret', options: ['expose' => true], methods: ['POST'])]
    public function newSecret(
        #[MapRequestPayload] NewSecretSantaDTO $secretSantaDTO,
        SecretSantaRepository $secretSantaRepository,
        SecretSantaMemberRepository $secretSantaMemberRepository,
        Security $security
    ): JsonResponse
    {
        $user = $security->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('Vous ne pouvez pas avoir accès.');
        }

        $secretSanta = new SecretSanta();

        $secretSanta
            ->setOwner($user)
            ->setLabel($secretSantaDTO->label);

        $secretSanta = $secretSantaRepository->create($secretSanta);

        if ($secretSantaDTO->registerMe) {
            $secretSantaMemberRepository->addMember($secretSanta, $user);
        }

        return $this->json($secretSanta);
    }
}