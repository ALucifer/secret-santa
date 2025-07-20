<?php

namespace App\Repository;

use App\Entity\DTO\Pagination;
use App\Entity\SecretSanta;
use App\Entity\SecretSantaMember;
use App\Entity\User;
use App\Services\Request\DTO\PaginationDTO;
use App\Services\Transformer\PaginateSecretSantaTransformer;
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
        private readonly PaginateSecretSantaTransformer $paginateSecretSantaTransformer,
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

    public function update(SecretSanta $secretSanta): void
    {
        $this->getEntityManager()->persist($secretSanta);
        $this->getEntityManager()->flush();
    }

    public function findPaginatedUserInvitedInSecretSanta(User $user, PaginationDTO $paginationDTO): Pagination
    {
        $query = $this
            ->createQueryBuilder('ss')
            ->select([
                'ss.id as id',
                'ss.label as label',
                'ss.state as state',
                'count(DISTINCT ssm2.id) as total'
            ])
            ->leftJoin('ss.members', 'ssm2', 'WITH', 'ss.state <> :ss_state or (ss.state = :ss_state and ssm2.state <> :ssm_state)')
            ->innerJoin('ss.members', 'ssm', 'WITH', 'ss.state <> :ss_state or (ss.state = :ss_state and ssm.state <> :ssm_state)')
            ->setParameter('ss_state', 'started')
            ->setParameter('ssm_state', 'wait_approval')
            ->groupBy('ss')
            ->where('ss.owner != :user')
            ->andWhere('ssm.user = :user')
            ->setParameter('user', $user)
            ->setParameter('user', $user)
            ->setMaxResults($paginationDTO->limit)
            ->setFirstResult(($paginationDTO->page - 1) * $paginationDTO->limit)
        ;

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $total = $paginator->count();

        return new Pagination(
            total: $total,
            items: $this->paginateSecretSantaTransformer->transformAll($query->getQuery()->getArrayResult()),
            pages: (int) ceil($total / $paginationDTO->limit),
            currentPage: $paginationDTO->page ,queryParam:
            $paginationDTO->queryParam,
        );
    }

    public function findPaginatedUserSecretsSanta(User $user, PaginationDTO $paginationDTO): Pagination
    {
        $query = $this
            ->createQueryBuilder('ss')
            ->select([
                'ss.id as id',
                'ss.label as label',
                'ss.state as state',
                'count(ssm.id) as total',
            ])
            ->innerJoin('ss.members', 'ssm', 'WITH', 'ss.state <> :ss_state or (ss.state = :ss_state and ssm.state <> :ssm_state)')
            ->setParameter('ss_state', 'started')
            ->setParameter('ssm_state', 'wait_approval')
            ->groupBy('ss')
            ->where('ss.owner = :user')
            ->setParameter('user', $user)
            ->setMaxResults($paginationDTO->limit)
            ->setFirstResult(($paginationDTO->page - 1) * $paginationDTO->limit)
        ;

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $total = $paginator->count();

        return new Pagination(
            total: $total,
            items: $this->paginateSecretSantaTransformer->transformAll($query->getQuery()->getArrayResult()),
            pages: (int) ceil($total / $paginationDTO->limit),
            currentPage: $paginationDTO->page ,queryParam:
            $paginationDTO->queryParam,
        );
    }
}
