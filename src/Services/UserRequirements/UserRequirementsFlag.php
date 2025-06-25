<?php

namespace App\Services\UserRequirements;

class UserRequirementsFlag
{
    private int $flags = 0;

    public function add(UserRequirementsEnumFlag $flag): void
    {
        $this->flags |= $flag->value;
    }

    public function addMany(array $flags): void
    {
        foreach ($flags as $flag) {
            if ($flag instanceof UserRequirementsEnumFlag) {
                $this->add($flag);
            }
        }
    }

    public function has(UserRequirementsEnumFlag $flag): bool
    {
        return $this->flags & $flag->value;
    }

    public function getRaw(): int
    {
        return $this->flags;
    }
}