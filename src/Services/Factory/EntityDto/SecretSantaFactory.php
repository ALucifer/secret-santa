<?php

declare(strict_types=1);

namespace App\Services\Factory\EntityDto;

use App\Entity\DTO\SecretSanta\SecretSanta as SecretSantaDto;
use App\Entity\DTO\SecretSanta\SecretSantaWithTotalMembers;
use App\Entity\DTO\Workflow\SecretSantaWorkflow;
use App\Entity\SecretSanta;

final class SecretSantaFactory implements SecretSantaFactoryInterface
{
    public function build(SecretSanta $secretSanta): SecretSantaDto
    {
        return new SecretSantaDto(
            id: $secretSanta->getId() ?? throw new \LogicException('Secret santa cannot be created without an ID.'),
            label: $secretSanta->getLabel(),
            state: $secretSanta->getState()->value,
            ownerId: $secretSanta->getOwner()->getId() ?? throw new \LogicException('Secret santa cannot be created without an owner ID.'),
            workflow: new SecretSantaWorkflow(
                canBeStarted: $secretSanta->canBeStarted(),
            )
        );
    }

    /**
     * @return array<SecretSantaWithTotalMembers>
     */
    public function buildWithTotalMembers(array $items): array
    {
        return array_map(
            fn (array $item) => new SecretSantaWithTotalMembers(
                id: $item['id'],
                label: $item['label'],
                state: $item['state']->value,
                totalMembers: $item['total'],
            ),
            $items,
        );
    }
}