<?php

namespace App\Repository;

use App\Entity\User;
use App\MessageHandler\RegisterNotification\RegisterNotification;
use App\Services\Request\DTO\NewMemberDTO;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends AbstractRepository<User>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MessageBusInterface $messageBus,
        private readonly RequestStack $requestStack,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly LoggerInterface $logger,
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

        $password = $this->passwordHasher->hashPassword($user, $newHashedPassword);

        $user->setPassword($password);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function create(User $user): void
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();

            $this->messageBus
                ->dispatch(
                    new RegisterNotification(
                        $user->getId() ?? throw new \LogicException('User id must be defined')
                    )
                );
        } catch (\Throwable $e) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('danger', $e->getMessage());
        }
    }

    public function createUserFromInvitation(NewMemberDTO $memberDTO): User
    {
        $user = User::fromInvitation($memberDTO->email);

        $hashedPassword = $this->passwordHasher->hashPassword($user, bin2hex(random_bytes(10)));

        $user
            ->setPassword($hashedPassword);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function update(User $user): void
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }

    public function updateAuthenticatedUser(User $user, bool $updatePassword): void
    {
        if ($updatePassword) {
            $password = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
        }

        $this->update($user);

        $this->tokenStorage->setToken(
            new UsernamePasswordToken(
                $user,
                'main',
                $user->getRoles(),
            ),
        );
    }
}
