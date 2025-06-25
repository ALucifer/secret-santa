<?php

namespace App\Services\UserRequirements\Strategies;

use App\Entity\User;
use App\Services\UserRequirements\UserRequirementsEnumFlag;
use App\Services\UserRequirements\UserRequirementsRedirectStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

class NewMemberRedirectStrategy implements UserRequirementsRedirectStrategyInterface
{
    public function supports(User $user): bool
    {
        return $user->isInvited() && $user->isVerified();
    }

    public function getFlag(): array|UserRequirementsEnumFlag
    {
        return [UserRequirementsEnumFlag::PSEUDO, UserRequirementsEnumFlag::PASSWORD];
    }
}