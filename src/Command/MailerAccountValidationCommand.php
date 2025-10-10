<?php

namespace App\Command;

use App\Factory\UserFactory;
use App\Repository\TokenRepository;
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
        private readonly AccountValidationMailer $mailer,
        private readonly TokenRepository $tokenRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);

        try {
            $user = UserFactory::createOne();
            $realUser = $user->_real();

            $token = $this->tokenRepository->createRegisterToken($realUser);

            $this->mailer->sendAccountValidationMail($realUser, $token);
            $outputStyle->success('Your email has been sent.');

            $user->_delete();

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $outputStyle->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
