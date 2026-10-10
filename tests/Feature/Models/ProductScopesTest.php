<?php

declare(strict_types=1);

use App\Models\Product;

beforeEach(function () {
    Product::factory()->create(['slug' => 'visible']);
    Product::factory()->outOfStock()->create(['slug' => 'sold-out']);
    Product::factory()->inactive()->create(['slug' => 'hidden']);
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
