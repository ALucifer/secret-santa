<?php

namespace App\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class AnonymousUser
{
    public function __construct(
        public readonly string $redirectRouteName = 'home',
    ) {
    }
}
