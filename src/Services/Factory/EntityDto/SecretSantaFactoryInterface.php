<?php

declare(strict_types=1);

namespace App\Services\Factory\EntityDto;

use App\Entity\DTO\SecretSanta\SecretSanta as SecretSantaDto;
use App\Entity\DTO\SecretSanta\SecretSantaWithTotalMembers;
use App\Entity\SecretSanta;

interface SecretSantaFactoryInterface
{
    public function build(SecretSanta $secretSanta): SecretSantaDto;

    /**
     * @return array<SecretSantaWithTotalMembers>
     */
    public function buildWithTotalMembers(array $items): array;
}
