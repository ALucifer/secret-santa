<?php

declare(strict_types=1);

namespace App\Services\Request\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class NewSecretSantaDTO
{
    public function __construct(
        #[Assert\Length(min: 5)]
        public string $label,
        public ?bool $registerMe,
    ) {
    }
}
