<?php

namespace App\Repository;

use App\Entity\SecretSanta;
use App\Entity\SecretSantaMember;
use App\Entity\User;
use App\MessageHandler\InvationHandler\InvitationNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @extends ServiceEntityRepository<SecretSantaMember>
 */
class SecretSantaMemberRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct($registry, SecretSantaMember::class);
    }

    public function create(SecretSanta $secretSanta, User $user): SecretSantaMember
    {
        $member = new SecretSantaMember();
        $member
            ->setSecretSanta($secretSanta)
            ->setUser($user);

        $this->getEntityManager()->persist($member);
        $this->getEntityManager()->flush();

        $this->messageBus->dispatch(new InvitationNotification($user, $secretSanta));
        return $member;
    }

    public function update(SecretSantaMember $secretSantaMember): void
    {
        $this->getEntityManager()->persist($secretSantaMember);
        $this->getEntityManager()->flush();
    }

    public function addMember(SecretSanta $secretSanta, User $user): void
    {
        $member = new SecretSantaMember();
        $member
            ->setSecretSanta($secretSanta)
            ->setUser($user);

        $secretSanta->addMember($member);

        $this->getEntityManager()->persist($secretSanta);
        $this->getEntityManager()->flush();
    }

    public function delete(SecretSantaMember $secretSantaMember): void
    {
        $this->getEntityManager()->remove($secretSantaMember);
        $this->getEntityManager()->flush();
    }
}
