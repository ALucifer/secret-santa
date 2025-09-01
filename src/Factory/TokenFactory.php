<?php

namespace App\Factory;

use App\Entity\Token;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Token>
 */
final class TokenFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Token::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'token' => self::faker()->text(),
            'validUntil' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'user' => UserFactory::new(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Token $token): void {})
        ;
    }
}
