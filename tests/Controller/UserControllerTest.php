<?php

namespace App\Tests\Controller;

use App\Factory\SecretSantaFactory;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Security\Role;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    public function testShouldRedirectToLoginWhenUserAccessToFormSecret(): void
    {
        $client = static::createClient();

        $client->request('GET', '/profile');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testShouldAuthorizeAccessToAuthenticatedUser(): void
    {
        $client = static::createClient();

        $userTest = UserFactory::createOne([ 'isVerified' => true, 'roles' => [ Role::USER ] ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userTest->getId());

        $client->loginUser($user);

        $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
    }

    public function testShouldAuthorizeButFailToCreateSecretSanta(): void
    {
        $client = static::createClient();

        $userTest = UserFactory::createOne([ 'isVerified' => true, 'roles' => [ Role::USER ] ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userTest->getId());

        $client->loginUser($user);

        $crawler = $client->request('GET', '/profile');

        $form = $crawler->filterXPath('//form[@name="secret_santa"]')->form();
        $form['secret_santa[label]'] = null;

        $crawler = $client->submit($form);
        $errors = $crawler->filterXPath('//form[@name="secret_santa"]//ul/li');

        $this->assertCount(2, $errors);

        foreach ($errors as $error) {
            $this->assertContains(
                $error->textContent,
                [
                    'Le titre doit être défini.',
                    'Le titre ne dois pas être vide.'
                ]
            );
        }
    }

    public function testShouldCreateSecretSanta(): void
    {
        $client = static::createClient();

        $userTest = UserFactory::createOne([ 'isVerified' => true, 'roles' => [ Role::USER ] ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userTest->getId());

        $client->loginUser($user);

        $crawler = $client->request('GET', '/profile');

        $form = $crawler->filterXPath('//form[@name="secret_santa"]')->form();
        $form['secret_santa[label]'] = 'Titre de test';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $title = $crawler->filter('h3')->text();

        $this->assertEquals('Titre de test', $title);
    }

    public function testShouldUnauthorizeToSeeSecretSanta(): void
    {
        $client = static::createClient();

        $userTest = UserFactory::createOne([ 'isVerified' => true, 'roles' => [ Role::USER ] ]);
        $unauthorizeUser = UserFactory::createOne([ 'isVerified' => true, 'roles' => [ Role::USER ] ]);

        $user = static::getContainer()->get(UserRepository::class)->find($unauthorizeUser->getId());

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $userTest
        ]);

        $client->loginUser($user);

        $client->request('GET', '/secret-santa/' . $secretSanta->getId());

        $this->assertResponseStatusCodeSame(403);
    }
}