<?php

namespace App\Services\Pagination;

use Doctrine\ORM\QueryBuilder;
use LogicException;

class Paginator
{
    public function paginate(
        QueryBuilder $query,
        int $page,
        int $limit,
        string $queryParam,
    ): Pagination {
        if ($page < 0) {
            throw new LogicException('Page must be greater than 0');
        }

        $totalQuery = clone $query;

        $total = (int) $totalQuery
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->select(
                sprintf(
                    'count(%s)',
                    $totalQuery->getRootAliases()[0]
                ),
            )
            ->getQuery()
            ->getSingleScalarResult();
            ;


        /** @var array<mixed> $results */
        $results = $query
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery()
            ->getResult();

        return new Pagination(
            $total,
            $results,
            (int) ceil($total / $limit),
            $page,
            $queryParam,
        );
    }
}