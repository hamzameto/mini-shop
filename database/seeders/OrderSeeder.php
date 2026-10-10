<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Create fifteen orders with one to three items each.
     * Every fifth order is a guest order.
     */
    public function run(): void
    {
        $customers = User::where('role', UserRole::Customer)->get();
        $products = Product::active()->inStock()->get();

        foreach (range(1, 15) as $number) {
            $items = $products->random(fake()->numberBetween(1, 3))->map(fn (Product $product) => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price_cents' => $product->price_cents,
                'quantity' => fake()->numberBetween(1, 3),
            ]);

            $customer = $customers->random();

            $buyer = $number % 5 === 0
                ? ['user_id' => null]
                : ['user_id' => $customer->id, 'email' => $customer->email, 'name' => $customer->name];

            $order = Order::factory()->create([
                ...$buyer,
                'status' => fake()->randomElement(OrderStatus::cases()),
                'total_cents' => $items->sum(fn (array $item) => $item['unit_price_cents'] * $item['quantity']),
                'placed_at' => fake()->dateTimeBetween('-30 days'),
            ]);

            $order->items()->createMany($items);
        }
    }
}
