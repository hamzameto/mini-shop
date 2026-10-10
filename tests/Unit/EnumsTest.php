<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\UserRole;

test('user role has the expected stored values', function () {
    expect(array_column(UserRole::cases(), 'value'))
        ->toBe(['customer', 'admin']);
});

test('order status has the expected stored values', function () {
    expect(array_column(OrderStatus::cases(), 'value'))
        ->toBe(['pending', 'paid', 'shipped', 'cancelled']);
});

test('an enum is built from its stored value', function () {
    expect(UserRole::from('admin'))->toBe(UserRole::Admin)
        ->and(OrderStatus::tryFrom('unknown'))->toBeNull();
});
