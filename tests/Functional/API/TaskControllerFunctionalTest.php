<?php

namespace App\Tests\Functional\API;

use App\Factory\TaskFactory;
use App\Tests\Controller\AuthenticateUserTrait;
use App\Tests\Helper\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskControllerFunctionalTest extends AbstractWebTestCase
{
    use ResetDatabase, Factories, AuthenticateUserTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    #[Group('done')]
    public function testShouldForbiddenUser(): void
    {
        TaskFactory::createOne();
        $this->client->request('GET', '/api/tasks/1');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testShouldReturnGoodTask(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}