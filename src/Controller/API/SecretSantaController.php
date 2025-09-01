<?php

namespace App\Controller\API;

use App\Entity\DTO\Member\Member;
use App\Entity\DTO\Member\Members;
use App\Entity\Member as EntityMember;
use App\Entity\SecretSanta;
use App\Entity\User;
use App\Repository\MemberRepository;
use App\Repository\SecretSantaRepository;
use App\Repository\UserRepository;
use App\Services\Factory\EntityDto\MemberFactoryInterface;
use App\Services\Factory\EntityDto\SecretSantaFactoryInterface;
use App\Services\Request\DTO\NewMemberDTO;
use App\Services\Request\DTO\NewSecretSantaDTO;
use Assert\Assertion;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    #[Route(
        path: '/secret-santa/{id}/register/member',
        name: 'registerMember',
        options: ['expose' => true],
        methods: ['POST'],
    )]
    #[IsGranted('ADD_MEMBER', 'secretSanta')]
    public function registerFromSecret(
        SecretSanta $secretSanta,
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)] NewMemberDTO $newMemberDTO,
        UserRepository $userRepository,
        MemberRepository $secretSantaMemberRepository,
        MemberFactoryInterface $memberFactory,
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

        return $this->json($memberFactory->build($member));
    }

    #[Route(
        path: '/secret-santa/{secretSanta}/delete/member/{member}',
        name: 'deleteMember',
        options: ['expose' => true],
        methods: ['DELETE'],
    )]
    public function deleteMember(
        SecretSanta      $secretSanta,
        EntityMember     $member,
        MemberRepository $secretSantaMemberRepository,
        LoggerInterface  $logger,
    ): JsonResponse {
        if ($secretSanta->getId() !== $member->getSecretSanta()->getId()) {
            return new JsonResponse(['error' => 'Member not found in this secret santa'], Response::HTTP_NOT_FOUND);
        }

        try {
            $secretSantaMemberRepository->delete($member);

            return new JsonResponse(null, Response::HTTP_OK);
        } catch (Throwable $e) {
            $logger->error($e->getMessage());
            return new JsonResponse(null, Response::HTTP_CONFLICT);
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