<?php

namespace App\Entity;

enum State: string
{
    case PENDING = "PENDING";
    case SUCCESS = "SUCCESS";
    case FAILURE = "FAILURE";
}
