<?php

declare(strict_types=1);

namespace App\Enum;

enum OrderResponseMessages: string
{
    case INVALID_INPUT = 'Invalid input';
    case NOT_FOUND = 'Order not found';
}
