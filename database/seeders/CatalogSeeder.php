<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Product names per category.
     *
     * @var array<string, list<string>>
     */
    private const array CATALOG = [
        'Coffee' => [
            'Ethiopia Yirgacheffe Beans', 'Colombia Supremo Beans', 'House Espresso Blend', 'Decaf Night Roast',
            'Brazil Santos Ground Coffee', 'Guatemala Antigua Beans', 'Cold Brew Coarse Grind',
        ],
        'Tea' => [
            'Sencha Green Tea', 'Earl Grey Classic', 'Darjeeling First Flush', 'Peppermint Leaves',
            'Chamomile Blossoms', 'Masala Chai Mix', 'Jasmine Pearls',
        ],
        'Brewing Gear' => [
            'Pour Over Dripper', 'French Press 1 Litre', 'Gooseneck Kettle', 'Hand Coffee Grinder',
            'Paper Filters 100 Pack', 'Stovetop Espresso Maker', 'Digital Brew Scale',
        ],
        'Cups and Mugs' => [
            'Stoneware Mug Grey', 'Double Wall Glass', 'Espresso Cup Set', 'Travel Tumbler Steel',
            'Enamel Camping Mug', 'Porcelain Teacup', 'Latte Bowl Blue',
        ],
        'Sweets' => [
            'Dark Chocolate 70 Percent', 'Almond Biscotti', 'Butter Shortbread', 'Salted Caramel Squares',
            'Hazelnut Nougat', 'Candied Orange Peel', 'Honey Waffles',
        ],
        'Gift Sets' => [
            'Coffee Starter Box', 'Tea Discovery Box', 'Barista Gift Set', 'Breakfast Hamper',
            'Chocolate Tasting Box', 'Two Mugs Gift Box', 'Seasonal Surprise Box',
        ],
    ];

    /**
     * Create six categories with seven products each.
     * In every category one product is sold out and one is inactive.
     */
    public function run(): void
    {
        foreach (self::CATALOG as $categoryName => $productNames) {
            $category = Category::factory()->create(['name' => $categoryName]);

            foreach ($productNames as $index => $productName) {
                Product::factory()
                    ->for($category)
                    ->when($index === 5, fn (ProductFactory $factory) => $factory->outOfStock())
                    ->when($index === 6, fn (ProductFactory $factory) => $factory->inactive())
                    ->create(['name' => $productName]);
            }
        }
    }
}
