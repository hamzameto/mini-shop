<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => Str::title(rtrim(fake()->unique()->sentence(3), '.')),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'description' => fake()->paragraph(),
            'price_cents' => fake()->numberBetween(199, 19999),
            'stock' => fake()->numberBetween(1, 50),
            'image_path' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the product is sold out.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the product is hidden from the shop.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
