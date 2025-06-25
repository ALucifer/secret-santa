<?php

namespace App\Services\Mailer;

use App\Entity\Token;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class AccountValidationMailer
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @param User $user
     * @param Token $token
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendAccountValidationMail(User $user, Token $token): void
    {
        $email = new TemplatedEmail();
        $email
            ->from('no-reply@secret-santa.com')
            ->to($user->getEmail())
            ->subject('Account Validation')
            ->htmlTemplate('emails/account-validation.html.twig')
            ->context([
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}