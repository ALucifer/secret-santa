<?php

declare(strict_types=1);

namespace App\Services\Factory\EntityDto;

use App\Entity\DTO\Member\Member as MemberDto;
use App\Entity\DTO\Member\MemberWishUserInformationsDto;
use App\Entity\Member;

final class MemberFactory implements MemberFactoryInterface
{
    public function build(Member $member): MemberDto
    {
        return new MemberDto(
            id: $member->getId() ?? throw new \LogicException('Member cannot be created without a member ID.'),
            email: $member->getUser()->getEmail(),
            invitationAccepted: $member->getState() === 'approved',
            secretSantaId: $member->getSecretSanta()->getId() ?? throw new \LogicException('Member cannot be created without a secret santa ID.'),
            userId: $member->getUser()->getId() ?? throw new \LogicException('Member cannot be created without a user ID.'),
        );
    }

    public function buildWithUserInformations(Member $member): MemberWishUserInformationsDto
    {
        return new MemberWishUserInformationsDto(
            id: $member->getId() ?? throw new \LogicException('Member with user informations cannot be created without a member ID.'),
            email: $member->getUser()->getEmail(),
        );
    }

    public function buildCollection(array $members): array
    {
        return array_map(fn(Member $member) => $this->build($member), $members);
    }
}