<?php

namespace App\Entity\DTO;

use App\Entity\Member as EntityMember;
use LogicException;

readonly class Member
{
    /**
     * @param int $id
     * @param string $email
     * @param bool $invitationAccepted
     */
    private function __construct(
        public int    $id,
        public string $email,
        public bool   $invitationAccepted,
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
            $member->getState() === 'approved'
        );
    }
}