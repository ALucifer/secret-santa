<?php

namespace App\Security\Voters;

use App\Entity\Member;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Member>
 */
class MemberVoter extends Voter
{
    private const ATTRIBUTE_ALLOWED = ['SHOW', 'NEW_WISH'];
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof Member) {
            return false;
        }

        if (!in_array($attribute, self::ATTRIBUTE_ALLOWED, true)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param Member $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            'SHOW' => $this->handleShow($user, $subject),
            'NEW_WISH' => $this->handleNewWish($subject),
            default => false,
        };
    }

    public function handleShow(User $user, Member $member): bool
    {
        $isSanta = $member
            ->getSecretSanta()
            ->getMembers()
            ->filter(
                function (Member $itemMember) use ($user, $member) {
                    if (!$itemMember->getSanta()) {
                        return false;
                    }

                    return $user->getId() === $itemMember->getUser()->getId()
                        && $itemMember->getSanta()->getId() === $member->getId();
                }
            )
            ->first();

        return $user->getId() === $member->getUser()->getId() || $isSanta;
    }

    public function handleNewWish(Member $member): bool
    {
        return $member->getWishItems()->count() < 10;
    }
}
