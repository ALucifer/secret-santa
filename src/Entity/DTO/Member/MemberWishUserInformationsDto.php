<?php

declare(strict_types=1);

namespace App\Entity\DTO\Member;

final readonly class MemberWishUserInformationsDto
{
    public function __construct(
        public int $id,
        public string $email,
    ) {
    }
}