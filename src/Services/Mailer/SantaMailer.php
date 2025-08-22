<?php

namespace App\Services\Mailer;

use App\Entity\SecretSanta;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

class SantaMailer
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function send(string $userEmail, SecretSanta $secretSanta, string $santaEmail): void
    {
        $email = new TemplatedEmail();

        $email
            ->from('no-reply@secret-santa.com')
            ->to($userEmail)
            ->subject(
                sprintf('🎁 Secret Santa %s - C\'est parti ! 🎄', $secretSanta->getLabel())
            )
            ->htmlTemplate('emails/santa.html.twig')
            ->context([
                'userEmail' => $userEmail,
                'planner' => $secretSanta->getOwner(),
                'secretSantaId' => $secretSanta->getId(),
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportException $e) {
            dd($e->getMessage());
        }
    }
}