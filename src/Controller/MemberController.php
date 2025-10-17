<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Member;
use App\Entity\SecretSanta;
use App\Services\Factory\EntityDto\MemberFactoryInterface;
use App\Services\Factory\EntityDto\SecretSantaFactoryInterface;
use App\Services\Factory\EntityDto\WishItemFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class MemberController extends AbstractController
{
    public function __construct(
        private readonly SecretSantaFactoryInterface $secretSantaFactory,
        private readonly MemberFactoryInterface $memberFactory,
    ) {
    }

    #[Route(
        path: '/secret-santa/{secretSanta}/members/{secretSantaMember}/wishlist',
        name: 'secret_santa_member_wishlist',
        options: ['expose' => true],
    )]
    #[IsGranted('SHOW', 'secretSantaMember')]
    public function memberList(
        SecretSanta $secretSanta,
        Member $secretSantaMember,
        WishItemFactoryInterface $wishItemFactory,
    ): Response {
        return $this->render(
            'secret-santa/member-wishlist.html.twig',
            [
                'member' => $this->memberFactory->buildWithUserInformations($secretSantaMember),
                'secretSanta' => $this->secretSantaFactory->build($secretSanta),
                'wishItems' => $wishItemFactory->buildCollection($secretSantaMember->getWishItems()->toArray()),
            ]
        );
    }
}
