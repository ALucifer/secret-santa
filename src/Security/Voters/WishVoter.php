<?php

namespace App\Security\Voters;

use App\Entity\SecretSantaMember;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use RuntimeException;

/**
 * @extends Voter<string, SecretSantaMember>
 */
class WishVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof SecretSantaMember) {
            return false;
        }

        if ($attribute !== 'NEW') {
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
        if ($attribute !== 'NEW' && $attribute !== 'DELETE') {
            throw new RuntimeException('Unsupported attribute: ' . $attribute);
        }

        return match ($attribute) {
            'NEW' => $this->handleNew($subject),
            default => false,
        };
    }

    private function handleNew(SecretSantaMember $subject): bool
    {
        return $subject->getWishItems()->count() < 10;
    }
}