<?php

namespace App\Tests\Functional\API;

use App\Tests\Controller\AuthenticateUserTrait;
use App\Tests\Helper\AbstractWebTestCase;
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

    public function testShouldForbiddenUser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testShouldReturnGoodTask(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}