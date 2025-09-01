<?php

namespace App\Factory;

use App\Entity\Member;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Member>
 */
final class MemberFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Member::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'state' => self::faker()->text(),
            'user' => UserFactory::new(),
            'secretSanta' => SecretSantaFactory::new(),
            'santa' => null,
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(SecretSantaMember $secretSantaMember): void {})
        ;
    }
}
