<?php

namespace App\DataFixtures;

use App\Entity\SecretSanta;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SecretSantaFixtures extends Fixture
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneByOrFail(['email' => 'test3@test.com']);

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $secretSanta = new SecretSanta();
            $secretSanta
                ->setOwner($user)
                ->setLabel($faker->word())
            ;

            $manager->persist($secretSanta);
        }

        $manager->flush();
    }
}