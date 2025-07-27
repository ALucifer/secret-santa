<?php

namespace App\Tests\Functional\API;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SecretSantaControllerFunctionalTest extends KernelTestCase
{
    /**
     * @group test
     * @dataProvider urls
     */
    public function testShouldUnauthorizeUser(string $url, string $method): void
    {
        $this->markTestIncomplete(
            \sprintf('Authorization test for "%s": "%s" not implemented', $method, $url)
        );
    }

    public function testShouldReturnJsonInsteadOfHTML(string $url, string $method): void
    {
        $this->markTestIncomplete(
            \sprintf('JSON response for "%s": "%s" not implemented', $method, $url)
        );
    }

    public function testShouldCreateNewMemberWithExistingUser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldCreateUserAndMemberWhenHeIsNotExisting(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldIndicateErrorBody(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldIndicateNotFoundWhenSecretSantaMemberIsNotInSecretSanta(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldDeleteMember(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldReturnMembersDtoInsteadOfEntity(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldIndicateErrorsInRequestForNewSecret(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldOnlyCreateSecretSanta(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldCreateSecretSantaAndOwnerAsMember(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function urls(): array
    {
        return [
            ['/secret-santa/{id}/register/member', 'POST'],
            ['/secret-santa/{secretId}/delete/member/{secretSantaMember}', 'DELETE'],
            ['/secret-santa/{id}/members', 'GET'],
            ['/secret-santa', 'POST'],
        ];
    }
}