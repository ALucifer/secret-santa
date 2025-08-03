<?php

namespace App\Tests\Functional\API;

use App\Tests\Helper\AbstractWebTestCase;

class WishControllerFunctionalTest extends AbstractWebTestCase
{
    public function testShouldForbiddenUser(string $url, string $method): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function deleteWish(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldNotFoundWishOnDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldReturnFormError(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldCreateNewWish(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}