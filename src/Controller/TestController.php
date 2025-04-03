<?php

namespace App\Controller;

use App\Services\Request\Attribute\PrefixPagination;
use App\Services\Request\PaginationDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test/{id}', name: 'test')]
    public function testAction(
        #[PrefixPagination(prefix: 'user_')] PaginationDTO $paginationDTO,
    ): Response
    {
        dd($paginationDTO);
    }
}