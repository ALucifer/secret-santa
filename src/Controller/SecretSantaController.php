<?php

namespace App\Controller;

use App\Entity\SecretSanta;
use App\Entity\User;
use App\Form\SecretSantaType;
use App\Repository\SecretSantaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use LogicException;

#[IsGranted('ROLE_USER')]
class SecretSantaController extends AbstractController
{
    #[Route('/secret-santa/new', name: 'secret_santa')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, Security $security, SecretSantaRepository $secretSantaRepository): Response
    {
        $form = $this->createForm(SecretSantaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SecretSanta $secretSanta */
            $secretSanta = $form->getData();

            $user = $security->getUser();

            if (!$user instanceof User) {
                throw new LogicException(sprintf('User must be an instance of %s.', User::class));
            }

            $secretSanta->setOwner($user);

            $secretSantaRepository->create($secretSanta);

            return $this->redirectToRoute(
                'secret_santa_view',
                [
                    'id' => $secretSanta->getId()
                ]
            );
        }

        return $this->render(
            'secret-santa/create.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }

    #[Route('/secret-santa/{id}', name: 'secret_santa_view')]
    public function view(SecretSanta $secretSanta): Response
    {
        dd($secretSanta);
    }
}