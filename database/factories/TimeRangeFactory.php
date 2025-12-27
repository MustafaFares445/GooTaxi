<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeRange>
 */
final class TimeRangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = fake()->randomElements(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], fake()->numberBetween(1, 3));

        return [
            'days' => $days,
            'from_time' => fake()->time('H:i', '18:00'),
            'to_time' => fake()->time('H:i', '23:59'),
            'price_percentage' => fake()->randomFloat(2, 10, 50),
            'start_price' => fake()->optional(0.7)->randomFloat(2, 5, 15),
            'price_of_going_per_km' => fake()->optional(0.7)->randomFloat(2, 2, 5),
            'return_price_per_km' => fake()->optional(0.7)->randomFloat(2, 2, 5),
        ];
    }

    public function weekend(): static
    {
        return $this->state(fn (array $attributes): array => [
            'days' => ['Fri', 'Sat', 'Sun'],
        ]);
    }

    public function weekday(): static
    {
        return $this->state(fn (array $attributes): array => [
            'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
        ]);
    }
}
