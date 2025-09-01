<?php

namespace App\Entity\DTO\Member;

use App\Entity\Member as EntityMember;

class Members implements \JsonSerializable
{
    /**
     * @param Member[] $members
     */
    public function __construct(
        public readonly array $members = []
    ) {
    }

    /**
     * @param Member[] $members
     */
    public static function fromEntity(array $members): self
    {
        return new self(
            array_map(
                fn (EntityMember $member) => Member::fromMember($member),
                $members,
            ),
        );
    }

    public function jsonSerialize(): mixed
    {
        return $this->members;
    }
}