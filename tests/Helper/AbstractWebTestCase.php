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
    private User $authenticatedUser;

    protected function getAuthenticatedClient(): KernelBrowser
    {
        $userFactory = UserFactory::createOne(['isVerified' => true, 'roles' => [Role::USER], 'pseudo' => 'user']);

        $this->authenticatedUser = static::getContainer()->get(UserRepository::class)->find($userFactory->getId());

        return static::createClient()->loginUser($this->authenticatedUser);
    }

    protected function getAuthenticatedJsonClient(): KernelBrowser
    {
        $userFactory = UserFactory::createOne(['isVerified' => true, 'roles' => [Role::USER], 'pseudo' => 'user']);

        $container = static::getContainer();

        $this->authenticatedUser = $container->get(UserRepository::class)->find($userFactory->getId());
        $jwtManager = $container->get(JWTTokenManagerInterface::class);

        return static::createClient(
            [],
            [
                'AUTHORIZATION' => 'Bearer ' . $jwtManager->create($this->authenticatedUser),
            ],
        );
    }
}