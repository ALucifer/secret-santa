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

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof Member) {
            return false;
        }

        if ($attribute != 'SHOW') {
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

        $isSanta = $subject
            ->getSecretSanta()
            ->getMembers()
            ->filter(
                function (Member $member) use ($user, $subject) {
                    if (!$member->getSanta()) {
                        return false;
                    }

                    return $user->getId() === $member->getUser()->getId() && $member->getSanta()->getId() === $subject->getId();
                })
        ->first();

        return $user->getId() === $subject->getUser()->getId() || $isSanta;
    }
}