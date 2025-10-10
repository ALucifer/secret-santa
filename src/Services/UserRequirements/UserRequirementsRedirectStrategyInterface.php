<?php

namespace App\Services\UserRequirements;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.user.requirements.redirect.strategy')]
interface UserRequirementsRedirectStrategyInterface
{
    public function supports(User $user): bool;

    public function getFlag(): array|UserRequirementsEnumFlag;
}
