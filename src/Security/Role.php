<?php

namespace App\Security;

enum Role: string
{
    case USER = 'ROLE_USER';
    case GUEST = 'ROLE_GUEST';
}
