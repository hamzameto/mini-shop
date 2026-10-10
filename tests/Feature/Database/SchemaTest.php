<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('column defaults are applied by the database', function () {
    $userId = DB::table('users')->insertGetId([
        'name' => 'Test User',
        'email' => 'user@example.com',
        'password' => 'hashed',
    ]);

    $productId = DB::table('products')->insertGetId([
        'category_id' => Category::factory()->create()->id,
        'name' => 'Novel',
        'slug' => 'novel',
        'description' => 'A novel.',
        'price_cents' => 1299,
    ]);

    $orderId = DB::table('orders')->insertGetId([
        'email' => 'buyer@example.com',
        'name' => 'Test Buyer',
        'street' => 'Main Street 1',
        'zip' => '12345',
        'city' => 'Berlin',
        'total_cents' => 1299,
        'placed_at' => now(),
    ]);

    $product = DB::table('products')->find($productId);

    expect(DB::table('users')->where('id', $userId)->value('role'))->toBe('customer')
        ->and($product->stock)->toBe(0)
        ->and($product->is_active)->toBeTrue()
        ->and(DB::table('orders')->where('id', $orderId)->value('status'))->toBe('pending');
});

test('a category with products cannot be deleted', function () {
    $product = Product::factory()->create();

    $product->category->delete();
})->throws(QueryException::class);

test('a product slug must be unique', function () {
    Product::factory()->count(2)->create(['slug' => 'novel']);
})->throws(QueryException::class);

test('deleting a user keeps the order without a user', function () {
    $order = Order::factory()->create();

    $order->user->delete();

    expect($order->fresh()->user_id)->toBeNull();
});

test('deleting a product keeps the order item', function () {
    $item = OrderItem::factory()->create();

    $item->product->delete();

    expect($item->fresh()->product_id)->toBeNull();
});

test('deleting an order deletes its items', function () {
    $item = OrderItem::factory()->create();

    $item->order->delete();

    expect(OrderItem::count())->toBe(0);
});
