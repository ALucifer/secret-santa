<?php

namespace App\Services\Request;

final readonly class PaginationDTO
{
    public function __construct(
        public int $page,
        public int $limit,
        public string $queryParam,
    ) {
    }
}