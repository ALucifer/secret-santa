<?php

declare(strict_types=1);

namespace App\Services\Transformer;

use App\Entity\DTO\SecretSantaWithTotalMembers;

final class PaginateSecretSantaTransformer
{
    /**
     * @param array $secretSantaList
     * @return array<SecretSantaWithTotalMembers>
     */
    public function transformAll(array $secretSantaList): array
    {
        return array_map(
            function(array $item) {
                return new SecretSantaWithTotalMembers(
                    id: $item['id'],
                    label: $item['label'],
                    state: $item['state'],
                    totalMembers: $item['total'],
                );
            },
            $secretSantaList
        );
    }
}