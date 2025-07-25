<?php

namespace App\Factory;

use App\Entity\SecretSanta;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SecretSanta>
 */
final class SecretSantaFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return SecretSanta::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'label' => self::faker()->text(60),
            'owner' => UserFactory::new(),
            'state' => self::faker()->randomElement(['standby', 'started'])
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(SecretSanta $secretSanta): void {})
        ;
    }
}
