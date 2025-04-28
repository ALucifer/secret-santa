<?php

namespace App\Entity;

use App\Repository\WishitemMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Request\DTO\NewWishItem;

#[ORM\Entity(repositoryClass: WishitemMemberRepository::class)]
class WishitemMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'wishitem_enum')]
    private WishitemType $type;

    #[ORM\Column(type: Types::JSON)]
    private array $data;

    public static function fromRequestDTO(NewWishItem $newWishItem): self
    {
        return (new self())
            ->setType(WishitemType::from($newWishItem->type))
            ->setData($newWishItem->data);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): WishitemType
    {
        return $this->type;
    }

    public function setType(WishitemType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
