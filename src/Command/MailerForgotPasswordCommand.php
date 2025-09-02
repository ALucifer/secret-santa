<?php

namespace App\Command;

use App\Factory\UserFactory;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Services\Mailer\ForgotPasswordMailer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mailer:forgot-password',
    description: 'Test mail for user password forgot',
)]
class MailerForgotPasswordCommand extends Command
{
    public function __construct(
        private readonly ForgotPasswordMailer $mailer,
        private readonly TokenRepository      $tokenRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = UserFactory::createOne();
        $realUser = $user->_real();

        $token = $this->tokenRepository->createForgotPasswordToken($realUser);

        try {
            $this->mailer->send($realUser, $token);
            $user->_delete();

            $io->success('Your email has been sent.');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
