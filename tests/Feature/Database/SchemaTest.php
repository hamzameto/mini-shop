<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Insert a category with one product and return the product id.
 */
function insertProduct(): int
{
    $categoryId = DB::table('categories')->insertGetId(['name' => 'Books', 'slug' => 'books']);

    return DB::table('products')->insertGetId([
        'category_id' => $categoryId,
        'name' => 'Novel',
        'slug' => 'novel',
        'description' => 'A novel.',
        'price_cents' => 1299,
    ]);
}

/**
 * Insert an order and return its id.
 */
function insertOrder(?int $userId = null): int
{
    return DB::table('orders')->insertGetId([
        'user_id' => $userId,
        'email' => 'buyer@example.com',
        'name' => 'Test Buyer',
        'street' => 'Main Street 1',
        'zip' => '12345',
        'city' => 'Berlin',
        'total_cents' => 1299,
        'placed_at' => now(),
    ]);
}

/**
 * Insert an order item and return its id.
 */
function insertOrderItem(int $orderId, int $productId): int
{
    return DB::table('order_items')->insertGetId([
        'order_id' => $orderId,
        'product_id' => $productId,
        'product_name' => 'Novel',
        'unit_price_cents' => 1299,
        'quantity' => 1,
    ]);
}

test('column defaults are applied', function () {
    $user = User::factory()->create();
    $product = DB::table('products')->find(insertProduct());
    $order = DB::table('orders')->find(insertOrder());

    expect(DB::table('users')->where('id', $user->id)->value('role'))->toBe('customer')
        ->and($product->stock)->toBe(0)
        ->and($product->is_active)->toBeTrue()
        ->and($order->status)->toBe('pending');
});

test('a category with products cannot be deleted', function () {
    $categoryId = DB::table('products')->where('id', insertProduct())->value('category_id');

    DB::table('categories')->where('id', $categoryId)->delete();
})->throws(QueryException::class);

test('a product slug must be unique', function () {
    insertProduct();
    insertProduct();
})->throws(QueryException::class);

test('deleting a user keeps the order without a user', function () {
    $user = User::factory()->create();
    $orderId = insertOrder($user->id);

    $user->delete();

    expect(DB::table('orders')->where('id', $orderId)->value('user_id'))->toBeNull();
});

test('deleting a product keeps the order item with its snapshot', function () {
    $productId = insertProduct();
    $itemId = insertOrderItem(insertOrder(), $productId);

    DB::table('products')->where('id', $productId)->delete();

    $item = DB::table('order_items')->find($itemId);

    expect($item->product_id)->toBeNull()
        ->and($item->product_name)->toBe('Novel');
});

test('deleting an order deletes its items', function () {
    $orderId = insertOrder();
    insertOrderItem($orderId, insertProduct());

    DB::table('orders')->where('id', $orderId)->delete();

    expect(DB::table('order_items')->count())->toBe(0);
});
