<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Repository\SecretSantaRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Random\Randomizer;

class MemberFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private SecretSantaRepository $secretSantaRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $secrets = $this->secretSantaRepository->findAll();
        $users = $this->userRepository->findAll();

        $random = new Randomizer();

        foreach ($secrets as $secret) {
            $keys = $random->pickArrayKeys($users, $random->getInt(1, count($users)));

            foreach ($keys as $key) {
                $member = new Member();
                $member->setUser($users[$key])->setSecretSanta($secret);

                $manager->persist($member);
            }
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
            SecretSantaFixtures::class,
        ];
    }
}
