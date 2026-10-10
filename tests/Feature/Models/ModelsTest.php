<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->category = Category::create(['name' => 'Books', 'slug' => 'books']);

    $this->product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Novel',
        'slug' => 'novel',
        'description' => 'A novel.',
        'price_cents' => 1299,
    ]);

    $this->order = Order::create([
        'user_id' => $this->user->id,
        'email' => 'buyer@example.com',
        'name' => 'Test Buyer',
        'street' => 'Main Street 1',
        'zip' => '12345',
        'city' => 'Berlin',
        'total_cents' => 2598,
        'placed_at' => '2026-10-10 12:00:00',
    ]);

    $this->item = OrderItem::create([
        'order_id' => $this->order->id,
        'product_id' => $this->product->id,
        'product_name' => 'Novel',
        'unit_price_cents' => 1299,
        'quantity' => 2,
    ]);
});

test('a user is a customer by default and the role is cast to the enum', function () {
    expect($this->user->role)->toBe(UserRole::Customer)
        ->and($this->user->fresh()->role)->toBe(UserRole::Customer);
});

test('the role cannot be mass assigned', function () {
    $user = User::create([
        'name' => 'Mallory',
        'email' => 'mallory@example.com',
        'password' => 'password',
        'role' => 'admin',
    ]);

    expect($user->fresh()->role)->toBe(UserRole::Customer);
});

test('a user has many orders', function () {
    expect($this->user->orders)->toHaveCount(1)
        ->and($this->user->orders->first()->is($this->order))->toBeTrue();
});

test('a category has many products and a product belongs to a category', function () {
    expect($this->category->products->first()->is($this->product))->toBeTrue()
        ->and($this->product->category->is($this->category))->toBeTrue();
});

test('a product has defaults and typed attributes', function () {
    $product = $this->product->fresh();

    expect($this->product->stock)->toBe(0)
        ->and($this->product->is_active)->toBeTrue()
        ->and($product->price_cents)->toBe(1299)
        ->and($product->stock)->toBe(0)
        ->and($product->is_active)->toBeTrue();
});

test('an order is pending by default and casts status and placed_at', function () {
    $order = $this->order->fresh();

    expect($this->order->status)->toBe(OrderStatus::Pending)
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->placed_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and($order->total_cents)->toBe(2598);
});

test('an order belongs to a user and has many items', function () {
    expect($this->order->user->is($this->user))->toBeTrue()
        ->and($this->order->items)->toHaveCount(1)
        ->and($this->order->items->first()->is($this->item))->toBeTrue();
});

test('a guest order has no user', function () {
    $this->order->update(['user_id' => null]);

    expect($this->order->fresh()->user)->toBeNull();
});

test('an order item belongs to an order and a product', function () {
    expect($this->item->order->is($this->order))->toBeTrue()
        ->and($this->item->product->is($this->product))->toBeTrue()
        ->and($this->product->orderItems->first()->is($this->item))->toBeTrue();
});

test('an order item keeps its snapshot when the product is deleted', function () {
    $this->product->delete();

    $item = $this->item->fresh();

    expect($item->product)->toBeNull()
        ->and($item->product_name)->toBe('Novel')
        ->and($item->unit_price_cents)->toBe(1299);
});
