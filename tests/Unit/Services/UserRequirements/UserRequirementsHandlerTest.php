<?php

namespace App\Tests\Unit\Services\UserRequirements;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Services\UserRequirements\UserRequirementsEnumFlag;
use App\Services\UserRequirements\UserRequirementsFlag;
use App\Services\UserRequirements\UserRequirementsHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @group test
 */
class UserRequirementsHandlerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    public function testShouldReturnGoodFlagsForNewMember(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $user = UserFactory::createOne([
            'email' => 'test@test.com',
            'pseudo' => null,
            'isInvited' => true,
            'isVerified' => true,
        ]);

        $securityMock = $this->createMock(Security::class);
        $securityMock->method('getUser')->willReturn($user);

        $container->set(Security::class, $securityMock);
        $handler = $container->get(UserRequirementsHandler::class);

        $this->assertEquals(3, $handler->handle()->getRaw());
    }

    public function testShouldReturnPseudoFlagForFirstUserLogin(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $user = UserFactory::createOne([
            'email' => 'test@test.com',
            'pseudo' => null,
            'isInvited' => false,
            'isVerified' => true,
        ]);

        $securityMock = $this->createMock(Security::class);
        $securityMock->method('getUser')->willReturn($user);

        $container->set(Security::class, $securityMock);
        $handler = $container->get(UserRequirementsHandler::class);

        $this->assertEquals(1, $handler->handle()->getRaw());
    }

    public function testShouldReturnNoFlag(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $user = UserFactory::createOne([
            'email' => 'test@test.com',
            'pseudo' => 'test',
            'isInvited' => false,
            'isVerified' => true,
        ]);

        $securityMock = $this->createMock(Security::class);
        $securityMock->method('getUser')->willReturn($user);

        $container->set(Security::class, $securityMock);
        $handler = $container->get(UserRequirementsHandler::class);

        $this->assertEquals(0, $handler->handle()->getRaw());
    }
}