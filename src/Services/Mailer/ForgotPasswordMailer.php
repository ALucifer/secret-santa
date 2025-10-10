<?php

namespace App\Services\Mailer;

use App\Entity\Token;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

class ForgotPasswordMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {
    }

    public function send(User $user, Token $token)
    {
        try {
            $mail = new TemplatedEmail();
            $mail
                ->from('no-reply@secret-santa.com')
                ->to($user->getEmail())
                ->subject('Mot de passe perdu ?')
                ->htmlTemplate('emails/forgot-password.html.twig')
                ->context([
                    'token' => $token,
                ]);

            $this->mailer->send($mail);
        } catch (TransportException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
