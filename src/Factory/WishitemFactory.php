<?php

namespace App\Factory;

use App\Entity\Wishitem;
use App\Entity\WishitemType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Wishitem>
 */
final class WishitemFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Wishitem::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'data' => [],
            'type' => WishitemType::MONEY,
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Wishitem $wishitem): void {})
        ;
    }
}
