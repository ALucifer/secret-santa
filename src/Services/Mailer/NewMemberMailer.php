<?php

namespace App\Services\Mailer;

use App\Entity\SecretSanta;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class NewMemberMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private LoginLinkHandlerInterface $loginLinkHandler,
    ) {
    }

    /**
     * @param User $user
     * @param SecretSanta $secretSanta
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendInvitation(User $user, SecretSanta $secretSanta): void
    {
        $email = new TemplatedEmail();
        $email
            ->from('no-reply@secret-santa.com')
            ->to($user->getEmail())
            ->subject('Invitation Secret Santa')
            ->htmlTemplate('emails/invitation.html.twig')
            ->context([
                'user' => $user,
                'authLink' => $this->loginLinkHandler->createLoginLink($user),
                'secretSantaTitle' => $secretSanta->getLabel(),
                'creator' => $secretSanta->getOwner()->getEmail()
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportException $e) {
            $this->logger->error(
                sprintf('Failed sending invitation email: %s', $e->getMessage()),
            );
        }
    }
}