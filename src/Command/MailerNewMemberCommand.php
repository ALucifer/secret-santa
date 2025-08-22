<?php

namespace App\Command;

use App\Factory\SecretSantaFactory;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Services\Mailer\NewMemberMailer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mailer:new-member',
    description: 'Test mail for new member',
)]
class MailerNewMemberCommand extends Command
{
    public function __construct(
        private readonly NewMemberMailer $mailer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = UserFactory::createOne();
        $owner = UserFactory::createOne(['pseudo' => 'mon super pseudo']);
        $secretSanta = SecretSantaFactory::createOne(['owner' => $owner]);

        try {
            $this->mailer->sendInvitation($user->_real(), $secretSanta);

            $user->_delete();
            $secretSanta->_delete();
            $owner->_delete();

            $io->success('Your email has been sent.');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
