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

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'state' => self::faker()->text(),
            'user' => UserFactory::new(),
            'secretSanta' => SecretSantaFactory::new(),
            'santa' => null,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(SecretSantaMember $secretSantaMember): void {})
        ;
    }
}
