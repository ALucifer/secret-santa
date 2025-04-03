<?php

namespace App\Services\Pagination;

final readonly class Pagination
{
    /**
     * @template T
     * @param int $total
     * @param array<T> $items
     * @param int $pages
     * @param int $currentPage
     * @param string $queryParam
     */
    public function __construct(
        public int $total,
        public array $items,
        public int $pages,
        public int $currentPage,
        public string $queryParam,
    ) {
    }
}