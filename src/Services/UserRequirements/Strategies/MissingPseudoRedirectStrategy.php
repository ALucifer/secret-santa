<?php

namespace App\Services\UserRequirements\Strategies;

use App\Entity\User;
use App\Services\UserRequirements\UserRequirementsEnumFlag;
use App\Services\UserRequirements\UserRequirementsRedirectStrategyInterface;

class MissingPseudoRedirectStrategy implements UserRequirementsRedirectStrategyInterface
{
    public function supports(User $user): bool
    {
        return !$user->getPseudo();
    }

    public function getFlag(): array|UserRequirementsEnumFlag
    {
        return UserRequirementsEnumFlag::PSEUDO;
    }
}
