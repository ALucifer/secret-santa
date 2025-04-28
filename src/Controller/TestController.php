<?php

namespace App\Controller;

use App\Entity\WishitemMember;
use App\Services\Request\DTO\NewWishItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\MessageHandler\NewWishItem\NewWishItem as NewWishItemMessage;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function testAction(
        #[MapRequestPayload]
        NewWishItem $newWishItem,
        MessageBusInterface $messageBus,
    ): Response
    {
        dd($newWishItem);
        $messageBus->dispatch(
            new NewWishItemMessage(
                WishitemMember::fromRequestDTO($newWishItem)
            ),
        );

        return $this->json(['envoyé']);
    }
}