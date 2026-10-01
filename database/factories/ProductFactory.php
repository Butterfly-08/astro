<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 299, 15000);
        $hasSale = fake()->boolean(60);
        $salePrice = $hasSale ? round($price * 0.8, 2) : null;

        return [
            'category_id'       => ProductCategory::factory(),
            'name'              => ucwords($name),
            'slug'              => Str::slug($name),
            'sku'               => 'AV-' . strtoupper(Str::random(8)),
            'short_description' => fake()->sentence(10),
            'description'       => fake()->paragraphs(3, true),
            'price'             => $price,
            'sale_price'        => $salePrice,
            'stock'             => fake()->numberBetween(5, 50),
            'status'            => 'active',
            'is_featured'       => fake()->boolean(30),
            'rating_avg'        => fake()->randomFloat(2, 4.0, 5.0),
            'total_reviews'     => fake()->numberBetween(5, 120),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => [
            'stock'  => 0,
            'status' => 'out_of_stock',
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn () => [
            'stock' => 3,
        ]);
    }
}
