<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Security\Role;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected User $authenticatedUser;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $userFactory = UserFactory::createOne(['isVerified' => true, 'roles' => [Role::USER], 'pseudo' => 'user']);
        $this->authenticatedUser = static::getContainer()->get(UserRepository::class)->find($userFactory->getId());
    }

    protected function getAuthenticatedClient(): KernelBrowser
    {
        return $this->client->loginUser($this->authenticatedUser);
    }

    protected function getAuthenticatedJsonClient(): KernelBrowser
    {
        $jwtManager = static::getContainer()->get(JWTTokenManagerInterface::class);

        $this->client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $jwtManager->create($this->authenticatedUser));;

        return $this->client;
    }
}