<?php

namespace App\Entity\DTO\Member;

use App\Entity\Member as EntityMember;
use LogicException;

final readonly class Member
{
    public function __construct(
        public int $id,
        public string $email,
        public bool $invitationAccepted,
        public int $secretSantaId,
        public int $userId,
    ) {
    }

    /**
     * @param EntityMember $member
     * @return self
     */
    public static function fromMember(EntityMember $member): self
    {
        return new self(
            $member->getId() ?? throw new LogicException('Member cannot be created without a member ID.'),
            $member->getUser()->getEmail(),
            $member->getState() === 'approved',
            $member->getSecretSanta()->getId(),
            $member->getUser()->getId()
        );
    }
}
