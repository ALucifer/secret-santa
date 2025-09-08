<?php

namespace App\Factory;

use App\Entity\State;
use App\Entity\Task;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Task::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'data' => [],
            'state' => State::PENDING,
            'created_at' => new \DateTimeImmutable(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}
