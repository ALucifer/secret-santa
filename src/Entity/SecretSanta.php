<?php

namespace App\Entity;

use App\Repository\SecretSantaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SecretSantaRepository::class)]
class SecretSanta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(type: Types::STRING, length: 60)]
    private string $label;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'secretSantas')]
    #[JoinColumn(nullable: false)]
    private User $owner;

    /**
     * @var Collection<int, SecretSantaMember> $members
     */
    #[ORM\OneToMany(targetEntity: SecretSantaMember::class, mappedBy: 'secretSanta')]
    private Collection $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Collection<int, SecretSantaMember>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @param Collection<int, SecretSantaMember> $members
     * @return $this
     */
    public function setMembers(Collection $members): SecretSanta
    {
        $this->members = $members;
        return $this;
    }
}
