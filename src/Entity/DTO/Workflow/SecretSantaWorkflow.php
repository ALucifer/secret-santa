<?php

declare(strict_types=1);

namespace App\Entity\DTO\Workflow;

final readonly class SecretSantaWorkflow
{
    public function __construct(
        public bool $canBeStarted,
    ) {
    }
}