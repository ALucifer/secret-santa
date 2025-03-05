<?php

namespace App\Services\Mailer;

use App\Entity\Token;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Throwable;

class AccountValidationMailer
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function sendAccountValidationMail(User $user, Token $token)
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

        try {
            $this->mailer->send($email);
        } catch (Throwable $e) {
            dd($e->getMessage());
        }
    }
}