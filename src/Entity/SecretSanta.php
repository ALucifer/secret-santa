<?php

namespace App\Entity;

use App\Repository\SecretSantaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SecretSantaRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SecretSanta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[
        Assert\NotNull(message: 'Le titre doit être défini.'),
        Assert\Length(min: 5, minMessage: 'Le titre dois faire au minimum 5 charactères.'),
        Assert\NotBlank(message: 'Le titre ne dois pas être vide.'),
    ]
    #[ORM\Column(type: Types::STRING, length: 60)]
    #[Groups(['read'])]
    private string $label;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'secretSantas')]
    #[JoinColumn(nullable: false)]
    private User $owner;

    /**
     * @var Collection<int, Member> $members
     */
    #[ORM\OneToMany(
        targetEntity: Member::class,
        mappedBy: 'secretSanta',
        cascade: ['persist', 'remove'],
    )]
    private Collection $members;

    #[ORM\Column(type: Types::STRING, enumType: SecretSantaState::class)]
    private SecretSantaState $state;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!$this->id) {
            $this->state = SecretSantaState::STANDBY;
        }
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
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @param Collection<int, Member> $members
     * @return $this
     */
    public function setMembers(Collection $members): SecretSanta
    {
        $this->members = $members;
        return $this;
    }

    public function addMember(Member $member): SecretSanta
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function getState(): SecretSantaState
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = SecretSantaState::from($state);
    }

    public function canBeStarted(): bool
    {
        return $this->members->filter(
            function (Member $member) {
                return $member->getState() === 'approved';
            }
        )->count() >= 3;
    }
}
