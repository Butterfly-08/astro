<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name),
            'description' => fake()->sentence(),
            'icon'        => 'bi bi-gem',
            'status'      => 'active',
            'sort_order'  => fake()->numberBetween(1, 10),
            'is_featured' => fake()->boolean(40),
        ];
    }
}
