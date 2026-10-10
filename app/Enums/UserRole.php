<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The role of a user account. The string value is what the database stores.
 */
enum UserRole: string
{
    case Customer = 'customer';
    case Admin = 'admin';
}
