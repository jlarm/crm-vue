<?php

namespace App\Enums;

enum Rating: string
{
    case HOT = 'hot';
    case WARM = 'warm';
    case COLD = 'cold';
}
