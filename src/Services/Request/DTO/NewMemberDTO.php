<?php

namespace App\Services\Request\DTO;

use Symfony\Component\Validator\Constraints\Email;

class NewMemberDTO
{
    public function __construct(
        #[Email]
        public readonly string $email,
    ) {
    }
}
