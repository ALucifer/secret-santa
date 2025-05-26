<?php

namespace App\Security\Voters;

use App\Entity\SecretSanta;
use App\Entity\SecretSantaMember;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, SecretSanta>
 */
class SecretSantaVoter extends Voter
{
    private const ATTRIBUTE_ALLOWED = ['SHOW', 'START'];

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof SecretSanta) {
            return false;
        }

        if (!in_array($attribute, self::ATTRIBUTE_ALLOWED, true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            'SHOW' => $this->handleShow($subject, $token),
            'START' => $this->handleStart($subject, $token),
            default => false,
        };
    }

    private function handleShow(mixed $subject, TokenInterface $token): bool
    {
        $userAuthenticated = $token->getUser();

        $isOwner = $userAuthenticated->getId() === $subject->getOwner()->getId();

        $member = $subject
            ->getMembers()
            ->filter(
                function (SecretSantaMember $member) use ($userAuthenticated) {
                    return $member->getUser()->getId() === $userAuthenticated->getId();
                });



        return $isOwner || $member->count() > 0;
    }

    /**
     * @param SecretSanta $subject
     * @param TokenInterface $token
     * @return bool
     */
    private function handleStart(mixed $subject, TokenInterface $token): bool
    {
        $userAuthenticated = $token->getUser();

        return $userAuthenticated->getId() === $subject->getOwner()->getId();
    }
}