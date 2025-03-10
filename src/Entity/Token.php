<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'token')]
#[ORM\HasLifecycleCallbacks]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private DateTimeImmutable $validUntil;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tokens')]
    private User $user;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!$this->createdAt) {
            $this->createdAt = new DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): Token
    {
        $this->token = $token;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Token
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getValidUntil(): DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function setValidUntil(DateTimeImmutable $validUntil): Token
    {
        $this->validUntil = $validUntil;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Token
    {
        $this->user = $user;
        return $this;
    }

    public function isValid(): bool
    {
        return $this->validUntil->getTimestamp() >= time();
    }
}