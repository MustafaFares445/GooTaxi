<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdditionalPrice>
 */
final class AdditionalPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate realistic coordinates (example: around a city center)
        // Adjust these to match your actual service area
        $baseLat = fake()->randomFloat(8, -90, 90);
        $baseLng = fake()->randomFloat(8, -180, 180);

        return [
            'start_price' => fake()->randomFloat(2, 5, 20),
            'price_of_going_per_km' => fake()->randomFloat(2, 1.50, 4.00),
            'return_price_per_km' => fake()->randomFloat(2, 1.50, 4.00),
            'latitude' => $baseLat,
            'longitude' => $baseLng,
            'address' => fake()->address(),
        ];
    }
}
