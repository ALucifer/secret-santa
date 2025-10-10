<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/esi', name: 'esi_')]
class EsiController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function menu(): Response
    {
        return $this->render('esi/menu.html.twig');
    }
}
