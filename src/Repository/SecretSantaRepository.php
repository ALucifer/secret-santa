<?php

namespace App\Repository;

use App\Entity\SecretSanta;
use App\Entity\User;
use App\Services\Pagination\Pagination;
use App\Services\Pagination\Paginator;
use App\Services\Request\DTO\PaginationDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<SecretSanta>
 */
class SecretSantaRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private LoggerInterface $logger,
        private Paginator $paginator,
    ) {
        parent::__construct($registry, SecretSanta::class);
    }

    public function create(SecretSanta $secretSanta): SecretSanta
    {
        try {
            $this->getEntityManager()->persist($secretSanta);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        return $secretSanta;
    }

    public function findInvitedUserInSecretSanta(User $user, PaginationDTO $paginationDTO): Pagination
    {
        $query = $this
            ->createQueryBuilder('ss')
            ->leftJoin('ss.members', 'ssm')
            ->where('ss.owner != :user')
            ->andWhere('ssm.user = :user')
            ->setParameter('user', $user);

        return $this->paginator->paginate(
            $query,
            $paginationDTO->page,
            $paginationDTO->limit,
            $paginationDTO->queryParam
        );
    }

    public function findPaginatedUserSecretsSanta(User $user, PaginationDTO $paginationDTO): Pagination
    {
        $query = $this
            ->createQueryBuilder('ss')
            ->where('ss.owner = :user')
            ->setParameter('user', $user);

        return $this->paginator->paginate(
            $query,
            $paginationDTO->page,
            $paginationDTO->limit,
            $paginationDTO->queryParam,
        );
    }
}
