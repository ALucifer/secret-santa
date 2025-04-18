<?php

namespace App\Entity\DTO;

use App\Entity\SecretSantaMember;
use LogicException;

class Member
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly bool $invitationAccepted,
    ) {
    }

    public static function fromMember(SecretSantaMember $member): self
    {
        return new self(
            $member->getId() ?? throw new LogicException('Member cannot be created without a member ID.'),
            $member->getUser()->getEmail(),
            $member->getState() === 'approved'
        );
    }
}