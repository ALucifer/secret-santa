<?php

namespace App\Controller;

use App\Entity\SecretSantaMember;
use App\Entity\State;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Services\Request\DTO\NewWishItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\MessageHandler\NewWishItem\NewWishItem as NewWishItemMessage;

class TestController extends AbstractController
{
    #[Route('/test/{secretSantaMember}', name: 'test')]
    public function testAction(
        SecretSantaMember $secretSantaMember,
        #[MapRequestPayload] NewWishItem $newWishItem,
        MessageBusInterface $messageBus,
        TaskRepository $taskRepository,
    ): Response
    {
        $task = new Task();
        $task->setState(State::PENDING);

        $taskRepository->save($task);

        $messageBus->dispatch(
            new NewWishItemMessage(
                $newWishItem,
                $secretSantaMember->getId(),
                $task->getId()
            ),
        );

        return $this->json($task);
    }
}