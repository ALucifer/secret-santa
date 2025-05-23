<?php

namespace App\Services\Request\DTO\Wish;

readonly class Event implements WishDTOInterface
{
    public function __construct(
        public string $name,
        public \DateTimeImmutable $date,
    ) {
    }

    /**
     * @return array{ name: string, date: \DateTimeImmutable }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'date' => $this->date,
        ];
    }
}