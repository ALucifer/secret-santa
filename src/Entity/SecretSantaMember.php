<?php

namespace App\Entity;

use App\Repository\SecretSantaMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretSantaMemberRepository::class)]
class SecretSantaMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\ManyToOne(targetEntity: SecretSanta::class)]
    private SecretSanta $secretSanta;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: Types::STRING)]
    private string $state;

    public function __construct()
    {
        $this->state = 'wait_approval';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecretSanta(): SecretSanta
    {
        return $this->secretSanta;
    }

    public function setSecretSanta(SecretSanta $secretSanta): SecretSantaMember
    {
        $this->secretSanta = $secretSanta;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): SecretSantaMember
    {
        $this->user = $user;
        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): SecretSantaMember
    {
        $this->state = $state;

        return $this;
    }
}
