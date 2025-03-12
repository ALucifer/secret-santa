<?php

namespace App\Repository;

use App\Entity\User;
use App\MessageHandler\RegisterNotification\RegisterNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Throwable;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private UserPasswordHasherInterface $passwordHasher,
        private MessageBusInterface $messageBus,
        private RequestStack $requestStack,
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function create(User $user): void
    {
        try {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();

            $this->messageBus->dispatch(new RegisterNotification($user->getId() ?? throw new LogicException('User id must be defined')));
        } catch (\Throwable $e) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('danger', $e->getMessage());
        }
    }

    public function update(User $user): void
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (Throwable $e) {
            dd($e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return User
     */
    public function findOneByOrFail(array $criteria, ?array $orderBy = null): User
    {
        $user = $this->findOneBy($criteria, $orderBy);

        return $user ?? throw new NotFoundHttpException('User not found.');
    }
}
