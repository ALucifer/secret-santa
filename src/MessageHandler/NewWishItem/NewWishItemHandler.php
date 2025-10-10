<?php

namespace App\MessageHandler\NewWishItem;

use App\Entity\State;
use App\Entity\Wishitem;
use App\Entity\WishitemType;
use App\Repository\MemberRepository;
use App\Repository\TaskRepository;
use App\Repository\WishItemRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class NewWishItemHandler
{
    /**
     * @param WishItemRepository $repository
     * @param MemberRepository $memberRepository
     * @param TaskRepository $taskRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private WishItemRepository $repository,
        private MemberRepository $memberRepository,
        private TaskRepository $taskRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param NewWishItem $wishItem
     * @return void
     */
    public function __invoke(NewWishItem $wishItem): void
    {
        $this->logger->info(sprintf("Traitement de WishItem de type %s", $wishItem->wishItem()->type->value));
        $member = $this->memberRepository->find($wishItem->memberId());

        if (!$member) {
            throw new NotFoundHttpException();
        }

        $wishItemEntity = Wishitem::fromRequestDTOAndMember($wishItem->wishItem(), $member);

        if ($wishItem->wishItem()->type === WishitemType::GIFT) {
            $this->handleGift($wishItemEntity);
        }

        $this->repository->save($wishItemEntity);

        $this->updateTask($wishItem->taskId(), $wishItemEntity);
    }

    /**
     * @param int $taskId
     * @param Wishitem $wishItem
     * @return void
     */
    private function updateTask(int $taskId, Wishitem $wishItem): void
    {
        $task = $this->taskRepository->find($taskId);

        if (!$task) {
            throw new NotFoundHttpException();
        }

        $task->setData($wishItem->toArray());
        $task->setState(State::SUCCESS);

        $this->taskRepository->update($task);
    }

    /**
     * @param Wishitem $wishItemMember
     * @return void
     */
    private function handleGift(Wishitem $wishItemMember): void
    {
        $client = HttpClient::create();

        /** @var array{ url: string } $data */
        $data = $wishItemMember->getData();

        try {
            $response = $client->request(
                'GET',
                $data['url'],
                [
                    'headers' => [
                        // phpcs:disable
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language' => 'fr-FR,fr;q=0.9',
                        'Referer' => 'https://www.google.com',
                        // phpcs:enable
                    ],
                ]
            );

            $html = $response->getContent();
            $crawler = new Crawler($html);

            $ogNameTitle = $crawler->filterXPath('//meta[@name="og:title"]');
            $ogPropertyTitle = $crawler->filterXPath('//meta[@property="og:title"]');
            $ogNameImage = $crawler->filterXPath('//meta[@name="og:image"]');
            $ogPropertyImage = $crawler->filterXPath('//meta[@property="og:image"]');

            $wishItemMember->setData([ ...$wishItemMember->getData()]);

            if ($ogNameTitle->count() > 0) {
                $wishItemMember->setData([
                    ...$wishItemMember->getData(),
                    'title' => $ogNameTitle->first()->attr('content'),
                ]);
            }

            if ($ogPropertyTitle->count() > 0) {
                $wishItemMember->setData([
                    ...$wishItemMember->getData(),
                    'title' => $ogPropertyTitle->first()->attr('content'),
                ]);
            }

            if ($ogNameImage->count() > 0) {
                $wishItemMember->setData([
                    ...$wishItemMember->getData(),
                    'image' => $ogNameImage->first()->attr('content'),
                ]);
            }

            if ($ogPropertyImage->count() > 0) {
                $wishItemMember->setData([
                    ...$wishItemMember->getData(),
                    'image' => $ogPropertyImage->first()->attr('content'),
                ]);
            }
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
