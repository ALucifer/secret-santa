<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Random\Randomizer;

class UserFixtures extends Fixture
{
    public const DEFAULT_USER = 'default_user';
    public const CURRENT_USER = 'current_user';

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $currentUser = new User();
        $currentUser->setEmail('user@mail.com')->setPassword('test')->setIsVerified(true);

        $defaultUser = new User();
        $defaultUser->setEmail('default@user.com')->setPassword('test');

        $manager->persist($currentUser);
        $manager->persist($defaultUser);

        $random = new Randomizer();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user
                ->setEmail("user{$i}@mail.com")
                ->setPassword("user{$i}")
                ->setIsVerified((bool) $random->getInt(0,1));

            $manager->persist($user);
        }

        $manager->flush();

        $this->addReference(self::DEFAULT_USER, $defaultUser);
        $this->addReference(self::CURRENT_USER, $currentUser);
    }
}