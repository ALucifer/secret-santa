<?php

namespace App\MessageHandler\SantaHandler;

use App\Repository\SecretSantaMemberRepository;
use App\Repository\SecretSantaRepository;
use App\Services\Mailer\SantaMailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SantaHandler
{
    public function __construct(
        private SecretSantaRepository $secretSantaRepository,
        private SecretSantaMemberRepository $secretSantaMemberRepository,
        private SantaMailer $mailer,
    )
    {
    }

    public function __invoke(
        Santa $santa,
    ) {
        $authenticatedUserMember = $this->secretSantaMemberRepository->findOneBy(['id' => $santa->memberId()]);

        if (!$authenticatedUserMember->getSanta()) {
            throw new \LogicException('Member should have santa in this part of process.');
        }

        $secretSanta = $this->secretSantaRepository->findOneBy(['id' => $santa->secretSantaId()]);

        $this->mailer->send(
            $authenticatedUserMember->getUser()->getEmail(),
            $secretSanta,
            $authenticatedUserMember->getSanta()->getUser()->getEmail()
        );
    }
}