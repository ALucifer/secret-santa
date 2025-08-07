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
    use ResetDatabase, Factories;

    #[Group('done')]
    public function testShouldForbiddenUser(): void
    {
        TaskFactory::createOne();
        $this->client->request('GET', '/api/tasks/1');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    #[Group('done')]
    public function testShouldReturnGoodTask(): void
    {
        $client = $this->getAuthenticatedJsonClient();
        $task = TaskFactory::createOne();

        $client->request('GET', '/api/tasks/' . $task->getId());

        $this->assertResponseIsSuccessful();

        $expected = json_encode(['state' => $task->getState()]);

        $this->assertJsonStringEqualsJsonString($expected, $client->getResponse()->getContent());
    }

    #[Group('todo')]
    public function testShouldShouldAuthorizeOnlyCreator(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}