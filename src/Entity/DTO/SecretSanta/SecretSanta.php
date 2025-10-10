<?php

declare(strict_types=1);

namespace App\Entity\DTO\SecretSanta;

use App\Entity\DTO\Workflow\SecretSantaWorkflow;

final readonly class SecretSanta
{
    public function __construct(
        public int $id,
        public string $label,
        public string $state,
        public int $ownerId,
        public SecretSantaWorkflow $workflow,
    ) {
    }
}
