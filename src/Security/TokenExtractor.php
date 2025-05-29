<?php

namespace App\Security;

use App\Entity\Token;
use App\Repository\TokenRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class TokenExtractor
{
    private bool $isValid;
    private ?Token $token = null;

    public function __construct(
        private RequestStack $requestStack,
        private TokenRepository $tokenRepository,
    )
    {
        $request = $this->requestStack->getCurrentRequest();

        $tokenFromQuery = $request->query->getString('token');

        if (!$tokenFromQuery) {
            $this->isValid = false;
        }

        if (!$this->token && $tokenFromQuery) {
            $this->token = $this->tokenRepository->findOneBy(['token' => $tokenFromQuery]);

            $this->isValid = $this->token && $this->token->isValid();
        }
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}