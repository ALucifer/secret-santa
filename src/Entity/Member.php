<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\ManyToOne(targetEntity: SecretSanta::class, inversedBy: 'members')]
    private SecretSanta $secretSanta;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: Types::STRING)]
    private string $state;

    /**
     * @var Collection<int, Wishitem> $wishItems
     */
    #[ORM\OneToMany(targetEntity: Wishitem::class, mappedBy: 'member')]
    private Collection $wishItems;

    #[ORM\OneToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'santa_id', referencedColumnName: 'id')]
    private Member|null $santa = null;

    public function __construct()
    {
        $this->state = 'wait_approval';
        $this->wishItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecretSanta(): SecretSanta
    {
        return $this->secretSanta;
    }

    public function setSecretSanta(SecretSanta $secretSanta): Member
    {
        $this->secretSanta = $secretSanta;

        $secretSanta->addMember($this);

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Member
    {
        $this->user = $user;

        $user->addParticipationSecretSantaHasMember($this);

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): Member
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, Wishitem>
     */
    public function getWishItems(): Collection
    {
        return $this->wishItems;
    }

    public function addWishItem(Wishitem $wishItem): Member
    {
        if (!$this->wishItems->contains($wishItem)) {
            $this->wishItems->add($wishItem);
            $wishItem->setMember($this);
        }

        return $this;
    }

    public function removeWishItem(Wishitem $wishItem): Member
    {
        if ($this->wishItems->contains($wishItem)) {
            $this->wishItems->removeElement($wishItem);
        }

        return $this;
    }

    /**
     * @param Collection<int, Wishitem> $wishItems
     * @return void
     */
    public function setWishItems(Collection $wishItems): void
    {
        $this->wishItems = $wishItems;
    }

    public function getSanta(): ?Member
    {
        return $this->santa;
    }

    public function setSanta(?Member $santa): void
    {
        $this->santa = $santa;
    }
}
