<?php

namespace App\DataFixtures;

use App\Entity\SecretSanta;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Random\Randomizer;

class SecretSantaFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $random = new Randomizer();

        for ($i = 0; $i < 100; $i++) {

            $secretSanta = new SecretSanta();
            $secretSanta
                ->setOwner(
                    $random->getInt(0,1) === 1
                        ? $this->getReference(UserFixtures::DEFAULT_USER, User::class)
                        : $this->getReference(UserFixtures::CURRENT_USER, User::class)
                )
                ->setLabel($faker->word())
                ->setState($faker->randomElement(['standby', 'started']))
            ;

            $manager->persist($secretSanta);
        }

        $manager->flush();
    }

    /**
     * @return \class-string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}