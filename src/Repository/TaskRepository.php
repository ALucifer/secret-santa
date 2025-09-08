<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function update(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function delete(Task $task, bool $asTransaction): void
    {
        $this->getEntityManager()->remove($task);

        if (!$asTransaction) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByStatesAndDate(array $states, \DateTimeImmutable $date): ?array
    {
        return $this->createQueryBuilder('task')
            ->where('task.state in(:state)')
            ->andWhere('task.createdAt < :date')
            ->setParameter('state', $states)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
