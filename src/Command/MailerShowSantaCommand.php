<?php

namespace App\Command;

use App\Factory\SecretSantaFactory;
use App\Services\Mailer\SantaMailer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mailer:show-santa',
    description: 'Test mail for show who is your santa',
)]
class MailerShowSantaCommand extends Command
{
    public function __construct(
        private readonly SantaMailer $mailer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $secretSanta = SecretSantaFactory::createOne();

            $this->mailer->send(
                userEmail: 'user@email.com',
                secretSanta: $secretSanta->_real(),
                santaEmail: 'santa@email.com'
            );

            $secretSanta->_delete();

            $io->success('Your email has been sent.');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
