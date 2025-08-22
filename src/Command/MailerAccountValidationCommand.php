<?php

namespace App\Command;

use App\Factory\UserFactory;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Services\Mailer\AccountValidationMailer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mailer:account-validation',
    description: 'Test mail for account validation',
)]
class MailerAccountValidationCommand extends Command
{
    public function __construct(
        private AccountValidationMailer $mailer,
        private TokenRepository $tokenRepository,
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $user = UserFactory::createOne()->_real();

            $token = $this->tokenRepository->createRegisterToken($user);

            $this->mailer->sendAccountValidationMail($user, $token);
            $io->success('Your email has been sent.');

            $this->userRepository->delete($user);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

    }
}
