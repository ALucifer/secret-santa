<?php

namespace App\Repository;

use App\Entity\User;
use App\MessageHandler\RegisterNotification\RegisterNotification;
use App\Security\Role;
use App\Services\Request\DTO\NewMemberDTO;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Throwable;

/**
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
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

            $this->messageBus->dispatch(new RegisterNotification($user->getId() ?? throw new LogicException('User id must be defined')));
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

    /**
     * @param User $user
     * @param array{password: string} $data
     * @return User
     */
    public function verifyMember(User $user, array $data): User
    {
        $hashPassword = $this->passwordHasher->hashPassword($user, $data['password']);

        $user
            ->setPassword($hashPassword)
            ->setIsVerified(true)
            ->setRoles([Role::USER]);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
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
}
