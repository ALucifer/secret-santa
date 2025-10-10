<?php

namespace App\Security\Voters;

use App\Entity\Member;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use RuntimeException;

/**
 * @extends Voter<string, Member>
 */
class WishVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof Member) {
            return false;
        }

        if ($attribute !== 'NEW') {
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
        if ($attribute !== 'NEW' && $attribute !== 'DELETE') {
            throw new RuntimeException('Unsupported attribute: ' . $attribute);
        }

        return match ($attribute) {
            'NEW' => $this->handleNew($subject),
            default => false,
        };
    }

    private function handleNew(Member $subject): bool
    {
        return $subject->getWishItems()->count() < 10;
    }
}
