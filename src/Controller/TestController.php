<?php

namespace App\Controller;

use App\Repository\SecretSantaMemberRepository;
use App\Services\Request\Attribute\PrefixPagination;
use App\Services\Request\PaginationDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function testAction(
        #[Target('secret_invitation')]
        WorkflowInterface $workflow,
        SecretSantaMemberRepository $memberRepository,
    ): Response
    {
        $member = $memberRepository->find(65);
        dd($workflow->can($member, 'to_approved'));
    }
}