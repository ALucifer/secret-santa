<?php

namespace App\Controller\API;

use App\Entity\WishitemMember;
use App\Repository\WishitemMemberRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[IsGranted("ROLE_USER")]
#[Route(path: '/api')]
class WishController extends AbstractController
{
    #[IsGranted("DELETE", "wishitemMember")]
    #[Route(path: '/wish/{id}', name: 'delete_wish', options: ['expose' => true], methods: ['DELETE'])]
    public function delete(
        WishitemMember $wishitemMember,
        WishitemMemberRepository $wishitemMemberRepository,
        LoggerInterface $logger
    ): Response {
        try {
            $wishitemMemberRepository->delete($wishitemMember);

            return $this->json([], Response::HTTP_NO_CONTENT);
        } catch (Throwable $e) {
            $logger->error($e->getMessage());
            return $this->json([], Response::HTTP_NOT_FOUND);
        }
    }
}