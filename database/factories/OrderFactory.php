<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first' => 'Test', 'last' => 'Customer', 'address' => 'Test address',
            'city' => 'Skopje', 'phone' => '070000000', 'email' => 'customer@example.test',
            'items' => '[]', 'total' => 1000, 'finished' => false,
        ];
    }
}
