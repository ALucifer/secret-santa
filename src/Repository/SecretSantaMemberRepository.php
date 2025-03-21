<?php

namespace App\Repository;

use App\Entity\SecretSanta;
use App\Entity\SecretSantaMember;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SecretSantaMember>
 */
class SecretSantaMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecretSantaMember::class);
    }

    public function create(SecretSanta $secretSanta, User $user): SecretSantaMember
    {
        $member = new SecretSantaMember();
        $member
            ->setSecretSanta($secretSanta)
            ->setUser($user);

        $this->getEntityManager()->persist($member);
        $this->getEntityManager()->flush();

        return $member;
    }
}
