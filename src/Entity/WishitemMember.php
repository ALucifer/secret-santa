<?php

namespace App\Entity;

use App\Repository\WishitemMemberRepository;
use App\Services\Request\DTO\Wish\Event;
use App\Services\Request\DTO\Wish\Gift;
use App\Services\Request\DTO\Wish\Money;
use App\Services\Request\DTO\Wish\WishDTOInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Request\DTO\NewWishItem;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: WishitemMemberRepository::class)]
class WishitemMember
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column
    ]
    #[Groups(['default'])]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(type: 'wishitem_enum')]
    #[Groups(['default'])]
    private WishitemType $type;

    /**
     * @var array<string, mixed> $data
     */
    #[ORM\Column(type: Types::JSON)]
    #[Groups(['default'])]
    private array $data;

    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'wishItems')]
    private Member $member;

    public static function fromRequestDTOAndMember(NewWishItem $newWishItem, Member $member): self
    {
        $wishItem = new self();

        $wishItem->setType($newWishItem->type)
            ->setData($newWishItem->data->toArray())
            ->setMember($member);

        $member->addWishItem($wishItem);

        return $wishItem;
    }

    /**
     * @return array{id: int|null, type: WishitemType, data: array<string, mixed> }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
        ];
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

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): self
    {
        $this->member = $member;

        return $this;
    }
}
