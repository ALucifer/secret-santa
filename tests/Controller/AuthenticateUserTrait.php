<?php

namespace App\Tests\Controller;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Security\Role;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait AuthenticateUserTrait
{
    private KernelBrowser $client;
    public function getAuthenticatedClient(): KernelBrowser
    {
        if (!method_exists(static::class, 'createClient')) {
            throw new \LogicException('This trait must be used in a class extending WebTestCase.');
        }

        if (null === $this->client) {
            $this->client = static::createClient();
        }

        $userFactory = UserFactory::createOne([ 'isVerified' => true, 'roles' => [ Role::USER ] ]);

        $user = static::getContainer()->get(UserRepository::class)->find($userFactory->getId());

        $this->client->loginUser($user);

        return $this->client;
    }
}