<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @template T of object
 * @template-extends ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
        * @phpstan-param array<string, mixed> $criteria
        * @phpstan-param array<string, string>|null $orderBy
        * @phpstan-return T
    */
    public function findOneByOrFail(array $criteria, ?array $orderBy = null): object
    {
        return $this->findOneBy($criteria, $orderBy) ?? throw new NotFoundHttpException(sprintf('%s not found.', $this->getEntityName()));
    }
}