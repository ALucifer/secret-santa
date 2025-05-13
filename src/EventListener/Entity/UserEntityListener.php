<?php

namespace App\EventListener\Entity;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'onPrePersist', entity: User::class)]
class UserEntityListener
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function onPrePersist(User $user, PrePersistEventArgs $event): void
    {
        $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());

        $user->setPassword($password);
    }
}