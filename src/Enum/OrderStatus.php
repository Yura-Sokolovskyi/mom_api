<?php

declare(strict_types=1);

namespace App\Enum;

enum OrderStatus: string
{
    case NEW = 'NEW';
    case PROCESSING = 'PROCESSING';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
