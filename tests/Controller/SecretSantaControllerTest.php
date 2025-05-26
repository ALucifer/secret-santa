<?php

namespace App\Tests\Controller;

use App\Factory\SecretSantaFactory;
use App\Factory\SecretSantaMemberFactory;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Security\Role;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecretSantaControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testShouldIndicateUserMustBelogged()
    {
        $secretSanta = SecretSantaFactory::createOne();
        SecretSantaMemberFactory::createOne([
            'secretSanta' => $secretSanta,
        ]);

        $this->client->request('GET', '/secret-santa/1/members/1/wishlist');

        $this->assertResponseRedirects('/login');
    }

    public function testShouldUnauthorizedWrongUserInWishList(): void
    {
        $secretSanta = SecretSantaFactory::createOne();
        $userFromFactory = UserFactory::createOne([
            'email' => 'user@mail.com',
            'password' => 'password',
            'roles' => [ Role::USER ],
            'isVerified' => true,
        ]);

        SecretSantaMemberFactory::createOne([
            'secretSanta' => $secretSanta,
        ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userFromFactory->getId());

        $this->client->loginUser($user);

        $this->client->request('GET', '/secret-santa/1/members/1/wishlist');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testShouldAuthorizeGoodUser(): void
    {
        $secretSanta = SecretSantaFactory::createOne();
        $userFromFactory = UserFactory::createOne([
            'email' => 'user@mail.com',
            'password' => 'password',
            'roles' => [ Role::USER ],
            'isVerified' => true,
        ]);

        SecretSantaMemberFactory::createOne([
            'secretSanta' => $secretSanta,
            'user' => $userFromFactory,
            'state' => 'approved'
        ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userFromFactory->getId());

        $this->client->loginUser($user);

        $this->client->request('GET', '/secret-santa/1/members/1/wishlist');
        $this->assertResponseIsSuccessful();
    }

    public function testShouldUnauthorizedWrongSantaInWishList(): void
    {
        $secretSanta = SecretSantaFactory::createOne();

        SecretSantaMemberFactory::createOne([
            'secretSanta' => $secretSanta,
            'state' => 'approved',
            'santa' => SecretSantaMemberFactory::createOne(),
        ]);

        $userFromFactory = UserFactory::createOne([
            'email' => 'user@mail.com',
            'password' => 'password',
            'roles' => [ Role::USER ],
            'isVerified' => true,
        ]);

        SecretSantaMemberFactory::createOne([
            'secretSanta' => $secretSanta,
            'user' => $userFromFactory,
            'state' => 'approved'
        ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userFromFactory->getId());

        $this->client->loginUser($user);

        $this->client->request('GET', '/secret-santa/1/members/1/wishlist');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testShouldAuthorizeGoodSanta(): void
    {
        $secretSanta = SecretSantaFactory::createOne();

        $firstMember = SecretSantaMemberFactory::createOne([
            'secretSanta' => $secretSanta,
            'state' => 'approved',
        ]);

        $userFromFactory = UserFactory::createOne([
            'email' => 'user@mail.com',
            'password' => 'password',
            'roles' => [ Role::USER ],
            'isVerified' => true,
        ]);

        $secondMember = SecretSantaMemberFactory::createOne([
            'secretSanta' => $secretSanta,
            'user' => $userFromFactory,
            'state' => 'approved',
            'santa' => $firstMember
        ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userFromFactory->getId());

        $this->client->loginUser($user);

        $this->client->request('GET', '/secret-santa/' . $secretSanta->getId() . '/members/' . $secondMember->getId() . '/wishlist');

        $this->assertResponseIsSuccessful();
    }
}