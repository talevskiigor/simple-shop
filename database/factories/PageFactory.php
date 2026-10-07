<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Test page '.fake()->unique()->numberBetween(1, 100000),
            'slug' => fake()->unique()->slug(),
            'body' => '<p>Test page content.</p>',
        ];
    }
}
