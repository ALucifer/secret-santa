<?php

namespace App\MessageHandler\NewWishItem;

use App\Entity\State;
use App\Entity\WishitemMember;
use App\Entity\WishitemType;
use App\Repository\SecretSantaMemberRepository;
use App\Repository\TaskRepository;
use App\Repository\WishitemMemberRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewWishItemHandler
{
    public function __construct(
        private WishitemMemberRepository $repository,
        private SecretSantaMemberRepository $memberRepository,
        private TaskRepository $taskRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(NewWishItem $wishItem): void
    {
        $this->logger->info(sprintf("Traitement de WishItem de type %s", $wishItem->wishItem()->type));
        $member = $this->memberRepository->find($wishItem->memberId());

        if (!$member) {
            throw new NotFoundHttpException();
        }

        $wishItemEntity = WishitemMember::fromRequestDTOAndMember($wishItem->wishItem(), $member);

        if ($wishItem->wishItem()->type === WishitemType::GIFT->value) {
            $this->handleGift($wishItemEntity);
        }

        $this->repository->save($wishItemEntity);

        $this->updateTask($wishItem->taskId(), $wishItemEntity);
    }

    private function updateTask(int $taskId, WishitemMember $wishItem): void
    {
        $task = $this->taskRepository->find($taskId);

        if (!$task) {
            throw new NotFoundHttpException();
        }

        $task->setData($wishItem->toArray());
        $task->setState(State::SUCCESS);

        $this->taskRepository->update($task);
    }

    private function handleGift(WishitemMember $wishItemMember): void
    {
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            $wishItemMember->getData()['url'],
        );

        $html = $response->getContent();
        $crawler = new Crawler($html);

        $ogTitle = $crawler->filterXPath('//meta[@property="og:title"]');
        $ogImage = $crawler->filterXPath('//meta[@property="og:image"]');

        $wishItemMember->setData([ ...$wishItemMember->getData(), 'image' => 'placeholder']);

        if ($ogTitle->count() > 0) {
            $wishItemMember->setData([ ...$wishItemMember->getData(), 'title' => $ogTitle->attr('content')]);
        }

        if ($ogImage->count() > 0) {
            $wishItemMember->setData([ ...$wishItemMember->getData(), 'image' => $ogImage->attr('content')]);
        }
    }
}