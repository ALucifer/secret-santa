<?php

namespace App\Repository;

use App\Entity\Token;
use App\Entity\User;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Token>
 */
class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function createRegisterToken(User $user): Token
    {
        $token = new Token();
        $token->setToken(bin2hex(random_bytes(16)));
        $token->setValidUntil((new DateTimeImmutable('now'))->add(new DateInterval('PT1H')));
        $token->setUser($user);

        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();

        return $token;
    }

    public function invalidToken(Token $token): void
    {
        $token->setValidUntil(new DateTimeImmutable('now'));

        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }
}