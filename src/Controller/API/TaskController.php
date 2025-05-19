<?php

namespace App\Controller\API;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route(path: '/api')]
class TaskController extends AbstractController
{

    #[Route(
        '/task/{id}',
        name: 'task',
        options: ['expose' => true],
        methods: ['GET']),
    ]
    public function task(
        Task $task,
    ): JsonResponse {
        return $this->json($task);
    }
}