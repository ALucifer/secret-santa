<?php

namespace App\Repository;

use App\Entity\SecretSanta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SecretSanta>
 */
class SecretSantaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecretSanta::class);
    }

    public function create(SecretSanta $secretSanta): void
    {
        try {
            $this->getEntityManager()->persist($secretSanta);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }
}
