<?php

namespace App\Security\Voters;

use App\Entity\SecretSantaMember;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, SecretSantaMember>
 */
class MemberVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof SecretSantaMember) {
            return false;
        }

        if ($attribute != 'show') {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param SecretSantaMember $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getId() === $subject->getUser()->getId();
    }
}