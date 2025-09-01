<?php

declare(strict_types=1);

namespace App\Services\Factory\EntityDto;

use App\Entity\DTO\Member\Member as MemberDto;
use App\Entity\DTO\Member\MemberWishUserInformationsDto;
use App\Entity\Member;

interface MemberFactoryInterface
{
    public function build(Member $member): MemberDto;

    public function buildWithUserInformations(Member $member): MemberWishUserInformationsDto;

    /**
     * @param array<Member> $members
     * @return array<MemberDto>
     */
    public function buildCollection(array $members): array;
}