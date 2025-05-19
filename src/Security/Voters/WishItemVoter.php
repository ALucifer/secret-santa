<?php

namespace App\Security\Voters;

use App\Entity\User;
use App\Entity\WishitemMember;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, WishitemMember>
 */
class WishItemVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof WishitemMember) {
            return false;
        }

        if ('DELETE' !== $attribute) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $subject->getMember()->getUser()->getId() === $user->getId();
    }
}