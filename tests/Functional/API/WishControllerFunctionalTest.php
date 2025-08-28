<?php

namespace App\Tests\Functional\API;

use App\Factory\MemberFactory;
use App\Factory\WishitemFactory;
use App\Repository\WishItemRepository;
use App\Tests\Helper\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\Group;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WishControllerFunctionalTest extends AbstractWebTestCase
{
    use ResetDatabase, Factories;

    protected function setUp(): void
    {
        parent::setUp();

        WishitemFactory::createOne();
        MemberFactory::createOne();
    }

    #[Group('done')]
    public function testShouldForbiddenUser(): void
    {
        $this->client->request('DELETE', '/api/wish/1');
        $this->assertResponseStatusCodeSame(401);
    }

    #[Group('done')]
    public function testShouldForbiddenUserInNewWish(): void
    {
        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/secret-santa/members/1/wish',
            parameters: [
                'type' => 'MONEY',
                'data' => ['price' => 10],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    #[Group('done')]
    public function testDeleteWish(): void
    {
        $member = MemberFactory::createOne([
            'user' => $this->authenticatedUser,
        ]);
        $wishItem = WishItemFactory::createOne([
            'member' => $member,
        ]);

        $repository = $this->getContainer()->get(WishItemRepository::class);
        $wishItems = $repository->findAll();

        $this->assertCount(2, $wishItems);

        $client = $this->getAuthenticatedJsonClient();

        $client->jsonRequest(
            method: 'DELETE',
            uri: '/api/wish/' . $wishItem->getId(),
        );

        $this->assertResponseIsSuccessful();

        $newTotalItems = $repository->findAll();
        $this->assertCount(1, $newTotalItems);
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

    public function testShouldUnathorizedUserWhoHaveNotAcceptInvitation(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public static function getRoutes(): array
    {
        return [
//            ['/api/wish/1', 'DELETE'],
            ['/api/secret-santa/members/1/wish', 'POST'],
        ];
    }
}