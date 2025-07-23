<?php

namespace App\Entity;

enum SecretSantaState: string
{
    case STANDBY = 'standby';
    case STARTED = 'started';
    case FINISH = 'finish';
}
