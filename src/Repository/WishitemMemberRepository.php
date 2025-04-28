<?php

namespace App\Repository;

use App\Entity\WishitemMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WishitemMember>
 */
class WishitemMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WishitemMember::class);
    }

    public function save(WishitemMember $wishitemMember): WishitemMember
    {
        $this->getEntityManager()->persist($wishitemMember);
        $this->getEntityManager()->flush();

        return $wishitemMember;
    }
}
