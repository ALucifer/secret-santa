<?php

namespace App\Entity\DTO;

use App\Entity\SecretSantaMember;

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
     * @param SecretSantaMember[] $members
     */
    public static function fromEntity(array $members): self
    {
        return new self(
            array_map(
                fn (SecretSantaMember $member) => Member::fromMember($member),
                $members,
            ),
        );
    }

    public function jsonSerialize(): mixed
    {
        return $this->members;
    }
}