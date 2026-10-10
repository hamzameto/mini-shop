<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

test('an order item factory creates the whole chain of related rows', function () {
    $item = OrderItem::factory()->create();

    expect($item->order->user)->toBeInstanceOf(User::class)
        ->and($item->product->category)->toBeInstanceOf(Category::class)
        ->and(Order::count())->toBe(1)
        ->and(Product::count())->toBe(1);
});

test('the slug follows the name', function () {
    $category = Category::factory()->create(['name' => 'Board Games']);
    $product = Product::factory()->create(['name' => 'Chess Set']);

    expect($category->slug)->toBe('board-games')
        ->and($product->slug)->toBe('chess-set');
});

test('a product is active and in stock by default', function () {
    $product = Product::factory()->create();

    expect($product->is_active)->toBeTrue()
        ->and($product->stock)->toBeGreaterThan(0);
});

test('product states', function () {
    expect(Product::factory()->outOfStock()->create()->stock)->toBe(0)
        ->and(Product::factory()->inactive()->create()->is_active)->toBeFalse();
});

test('user states', function () {
    expect(User::factory()->create()->role)->toBe(UserRole::Customer)
        ->and(User::factory()->admin()->create()->role)->toBe(UserRole::Admin);
});

test('an order is pending and has a user by default, a guest order has none', function () {
    $order = Order::factory()->create();

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->user)->toBeInstanceOf(User::class)
        ->and(Order::factory()->guest()->create()->user)->toBeNull();
});

test('many products can be created for one category', function () {
    $category = Category::factory()->has(Product::factory()->count(3))->create();

    expect($category->products)->toHaveCount(3)
        ->and(Category::count())->toBe(1);
});
