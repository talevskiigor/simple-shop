<?php

namespace Database\Factories;

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
        return [
            'name' => 'Test product '.fake()->unique()->numberBetween(1, 100000),
            'slug' => fake()->unique()->slug(),
            'description' => '<p>Test product description.</p>',
            'model' => fake()->unique()->bothify('TEST-####??'),
            'image' => 'images/test-product.jpg',
            'price' => 1000, 'discount' => 0, 'tax_id' => 1,
            'quantity' => 5, 'active' => true,
        ];
    }
}
