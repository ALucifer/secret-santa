<?php

namespace App\Repository;

use App\Entity\Wishitem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wishitem>
 */
class WishitemMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishitem::class);
    }

    public function save(Wishitem $wishitemMember): Wishitem
    {
        $this->getEntityManager()->persist($wishitemMember);
        $this->getEntityManager()->flush();

        return $wishitemMember;
    }

    public function delete(Wishitem $wishitemMember): void
    {
        $this->getEntityManager()->remove($wishitemMember);
        $this->getEntityManager()->flush();
    }
}
