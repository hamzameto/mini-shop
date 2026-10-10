<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Product;

beforeEach(function () {
    $category = Category::create(['name' => 'Books', 'slug' => 'books']);

    $products = [
        'visible' => ['is_active' => true, 'stock' => 5],
        'sold-out' => ['is_active' => true, 'stock' => 0],
        'hidden' => ['is_active' => false, 'stock' => 5],
    ];

    foreach ($products as $slug => $attributes) {
        Product::create([
            'category_id' => $category->id,
            'name' => $slug,
            'slug' => $slug,
            'description' => 'A product.',
            'price_cents' => 1000,
            ...$attributes,
        ]);
    }
});

test('active returns only active products', function () {
    expect(Product::active()->pluck('slug')->sort()->values()->all())
        ->toBe(['sold-out', 'visible']);
});

test('inStock returns only products with stock', function () {
    expect(Product::inStock()->pluck('slug')->sort()->values()->all())
        ->toBe(['hidden', 'visible']);
});

test('scopes can be chained', function () {
    expect(Product::active()->inStock()->pluck('slug')->all())
        ->toBe(['visible']);
});
