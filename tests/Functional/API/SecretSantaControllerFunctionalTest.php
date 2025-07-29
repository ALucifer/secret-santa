<?php

namespace App\Tests\Functional\API;

use App\Entity\Member;
use App\Entity\SecretSanta;
use App\Factory\MemberFactory;
use App\Factory\SecretSantaFactory;
use App\Tests\Controller\AuthenticateUserTrait;
use App\Tests\Helper\AbstractWebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecretSantaControllerFunctionalTest extends AbstractWebTestCase
{
    use ResetDatabase, Factories, AuthenticateUserTrait;

    private KernelBrowser $client;

    private SecretSanta $secretSanta;
    private Member $member;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->secretSanta = SecretSantaFactory::createOne();
        $this->member = MemberFactory::createOne([
            'secretSanta' => $this->secretSanta
        ]);
    }

    /**
     * @group done
     * @dataProvider urls
     */
    public function testShouldUnauthorizeUser(string $url, string $method): void
    {
        $url = str_replace(
            [ '{id}', '{secretSantaMember}' ],
            [ $this->secretSanta->getId(), $this->member->getId() ],
            $url
        );

        $this->client->request($method, $url);

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @group done
     */
    public function testShouldCreateNewMemberWithExistingUser(): void
    {
        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $this->authenticatedUser
        ]);

        $payload = [
            'email' => 'user@mail.com'
        ];

        $client->jsonRequest(
            'POST',
            '/api/secret-santa/' . $secretSanta->getId() . '/register/member',
            $payload,
        );

        $this->assertResponseIsSuccessful();
    }

    public function testShouldNotCreateNewMemberIfItsNotOwner(): void
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
            ['/api/secret-santa/{id}/register/member', 'POST'],
            ['/api/secret-santa/{id}/delete/member/{secretSantaMember}', 'DELETE'],
            ['/api/secret-santa/1/members', 'GET'],
            ['/api/secret-santa', 'POST'],
        ];
    }
}