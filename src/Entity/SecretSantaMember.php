<?php

namespace App\Entity;

use App\Repository\SecretSantaMemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, WishitemMember> $wishItems
     */
    #[ORM\OneToMany(targetEntity: WishitemMember::class, mappedBy: 'member')]
    private Collection $wishItems;

    #[ORM\OneToOne(targetEntity: SecretSantaMember::class)]
    #[ORM\JoinColumn(name: 'santa_id', referencedColumnName: 'id')]
    private SecretSantaMember|null $santa = null;

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

    /**
     * @return Collection<int, WishitemMember>
     */
    public function getWishItems(): Collection
    {
        return $this->wishItems;
    }

    public function addWishItem(WishitemMember $wishItem): SecretSantaMember
    {
        if (!$this->wishItems->contains($wishItem)) {
            $this->wishItems->add($wishItem);
            $wishItem->setMember($this);
        }

        return $this;
    }

    public function removeWishItem(WishitemMember $wishItem): SecretSantaMember
    {
        if ($this->wishItems->contains($wishItem)) {
            $this->wishItems->removeElement($wishItem);
        }

        return $this;
    }

    /**
     * @param Collection<int, WishitemMember> $wishItems
     * @return void
     */
    public function setWishItems(Collection $wishItems): void
    {
        $this->wishItems = $wishItems;
    }

    public function getSanta(): ?SecretSantaMember
    {
        return $this->santa;
    }

    public function setSanta(?SecretSantaMember $santa): void
    {
        $this->santa = $santa;
    }
}
