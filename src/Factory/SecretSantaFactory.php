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

    protected function defaults(): array|callable
    {
        return [
            'label' => self::faker()->text(60),
            'owner' => UserFactory::new(),
            'state' => self::faker()->randomElement(['standby', 'started'])
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(SecretSanta $secretSanta): void {})
        ;
    }
}
