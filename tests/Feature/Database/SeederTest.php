<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('the seeder creates one admin and five customers', function () {
    expect(User::where('role', UserRole::Admin)->count())->toBe(1)
        ->and(User::where('role', UserRole::Customer)->count())->toBe(5)
        ->and(User::where('email', 'admin@example.com')->sole()->role)->toBe(UserRole::Admin);
});

test('the seeder creates the catalogue', function () {
    expect(Category::count())->toBe(6)
        ->and(Product::count())->toBe(42)
        ->and(Product::where('stock', 0)->count())->toBe(6)
        ->and(Product::where('is_active', false)->count())->toBe(6);
});

test('the seeder creates customer and guest orders', function () {
    expect(Order::count())->toBe(15)
        ->and(Order::whereNull('user_id')->count())->toBe(3);
});

test('every seeded order total matches its items', function () {
    Order::with('items')->get()->each(function (Order $order) {
        $sum = $order->items->sum(fn (OrderItem $item) => $item->unit_price_cents * $item->quantity);

        expect($order->items)->not->toBeEmpty()
            ->and($order->total_cents)->toBe($sum);
    });
});

test('every seeded order item is a snapshot of its product', function () {
    OrderItem::with('product')->get()->each(function (OrderItem $item) {
        expect($item->product_name)->toBe($item->product->name)
            ->and($item->unit_price_cents)->toBe($item->product->price_cents);
    });
});
