<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BasePrice>
 */
final class BasePriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price_per_km' => fake()->randomFloat(2, 1.50, 3.50),
            'van_price_percentage' => fake()->randomFloat(2, 10, 30),
        ];
    }
}
