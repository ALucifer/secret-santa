<?php

namespace App\Services\Request\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class PrefixPagination
{
    public function __construct(
        public string $prefix,
        public int $limit = 10,
    ) {
    }
}