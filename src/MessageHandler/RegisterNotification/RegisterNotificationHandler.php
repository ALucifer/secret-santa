<?php

namespace App\MessageHandler\RegisterNotification;


use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Services\Mailer\AccountValidationMailer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
class RegisterNotificationHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenRepository $tokenRepository,
        private AccountValidationMailer $mailer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(RegisterNotification $registerNotification): void
    {
        try {
            $user = $this->userRepository->findOneByOrFail(['id' => $registerNotification->userId()]);
            $token = $this->tokenRepository->createRegisterToken($user);

            $this->mailer->sendAccountValidationMail($user, $token);
        } catch (Throwable $e) {
            $this->logger->error(
                sprintf('Register Message handler failed: %s', $e->getMessage()),
            );
        }
    }
}