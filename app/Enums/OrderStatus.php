<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The lifecycle state of an order. The string value is what the database stores.
 */
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
