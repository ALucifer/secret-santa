<?php

namespace App\Command;

use App\Entity\State;
use App\Repository\TaskRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:task:clean',
    description: 'Clean all finished tasks',
)]
class TaskCleanCommand extends Command
{
    public function __construct(
        private TaskRepository $taskRepository,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dateLimit = (new \DateTimeImmutable('NOW'))->sub(DateInterval::createFromDateString('7 days'));

        $tasks = $this->taskRepository->findByStatesAndDate(states: [State::SUCCESS->value, State::FAILURE->value], date: $dateLimit);


        try {
            foreach ($tasks as $task) {
                $io->info(
                    sprintf('Cleaning task "%s"', $task->getId()),
                );
                $this->taskRepository->delete($task, true);
            }

            $this->entityManager->flush();

            $io->success(
                sprintf('Cleaned %s tasks successfully.', count($tasks)),
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->entityManager->clear();
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
