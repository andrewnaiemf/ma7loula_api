<?php

namespace Database\Factories;

use App\Models\ProductBrand;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        $brand = ProductBrand::inRandomOrder()->first();
        $price = fake()->randomFloat(2, 200, 5000);
        return [
            'name' => fake()->words(2, 1),
            'description' => fake()->optional()->realText(200),
            'category_id' => $brand->product_category_id,
            'brand_id' => $brand->id,
            'vendor_id' => Vendor::inRandomOrder()->first()->id,
            'price' => $price,
            'price_before_discount' => fake()->optional(0.2)->randomFloat(2, $price, 5000),
            'stock' => fake()->numberBetween(1, 50),
            'status' => fake()->randomElement(['published', 'pending', 'rejected'])
        ];
    }
}
