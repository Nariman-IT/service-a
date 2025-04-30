<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case IN_WORK = 'inWork';
    case COMPLETED = 'completed';

}