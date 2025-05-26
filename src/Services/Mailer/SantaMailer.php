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

    public function send(string $authenticatedUserEmail, SecretSanta $secretSanta, string $santaEmail): void
    {
        $email = new TemplatedEmail();

        $email
            ->from('no-reply@secret-santa.com')
            ->to($authenticatedUserEmail)
            ->subject('Votre santa vient d\'arriver !')
            ->htmlTemplate('emails/santa.html.twig')
            ->context([
                'authenticatedUserEmail' => $authenticatedUserEmail,
                'secretSanta' => $secretSanta,
                'santaEmail' => $santaEmail,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportException $e) {
            dd($e->getMessage());
        }
    }
}