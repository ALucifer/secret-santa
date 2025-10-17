<?php

namespace App\Services\Request\DTO\Wish;

readonly class Gift implements WishDTOInterface
{
    public function __construct(
        public string $url,
    ) {
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
        ];
    }
}
