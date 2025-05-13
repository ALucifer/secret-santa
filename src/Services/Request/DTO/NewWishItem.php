<?php

namespace App\Services\Request\DTO;

use App\Entity\WishitemType;
use App\Validator\Constraints\WishItemData;
use Symfony\Component\Validator\Constraints as Assert;

#[WishItemData]
class NewWishItem
{
    #[Assert\Choice(callback: [WishitemType::class, 'values'], message: 'Wishitem type is not valid.')]
    public readonly string $type;
    /** @var array<mixed>  */
    public readonly array $data;

    /**
     * @param string $type
     * @param array<mixed> $data
     */
    public function __construct(
        string $type,
        array $data,
    ) {
        $this->type = strtoupper($type);
        $this->data = $data;
    }
}