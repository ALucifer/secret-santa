<?php

namespace App\Controller\API;

use App\Controller\AbstractMessengerController;
use App\Entity\Member;
use App\Entity\State;
use App\Entity\Task;
use App\Entity\Wishitem;
use App\MessageHandler\NewWishItem\NewWishItem as NewWishItemMessage;
use App\Repository\TaskRepository;
use App\Repository\WishItemRepository;
use App\Services\Request\DTO\NewWishItem;
use App\ValueResolver\WishValueResolver;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[Route(path: '/api')]
#[IsGranted("ROLE_USER")]
class WishController extends AbstractMessengerController
{
    #[IsGranted("DELETE", "wishItem")]
    #[Route(
        path: '/wish/{id}',
        name: 'delete_wish',
        options: ['expose' => true],
        methods: ['DELETE'],
    )]
    public function delete(
        Wishitem $wishItem,
        WishItemRepository $wishitemMemberRepository,
    ): Response {
        try {
            $wishitemMemberRepository->delete($wishItem);

            return $this->json([], Response::HTTP_NO_CONTENT);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return $this->json([], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route(
        path: '/secret-santa/members/{id}/wish',
        name: 'newWish',
        options: ['expose' => true],
        methods: ['POST']
    )]
    #[IsGranted('NEW_WISH', 'member')]
    public function newWish(
        Member $member,
        #[ValueResolver('wish_value')] NewWishItem $newWishItem,
        TaskRepository                             $taskRepository,
    ): JsonResponse {

        $task = new Task();
        $task->setState(State::PENDING);
        $task->setData(['type' => $newWishItem->type]);

        $taskRepository->save($task);

        if (null === $task->getId() || null === $member->getId()) {
            throw new RuntimeException('Task ID and Secret Santa can not be null');
        }

        $this->messageBus->dispatch(
            new NewWishItemMessage(
                $newWishItem,
                $member->getId(),
                $task->getId()
            ),
        );

        return $this->json($task);
    }
}