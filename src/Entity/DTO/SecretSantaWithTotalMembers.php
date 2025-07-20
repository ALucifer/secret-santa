<?php

declare(strict_types=1);

namespace App\Entity\DTO;

final class SecretSantaWithTotalMembers
{
    public function __construct(
      public int $id,
      public string $label,
      public string $state,
      public int $totalMembers,
    ) {
    }
}