<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Driver;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
final class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locations = [
            'Airport Terminal 1', 'Airport Terminal 2', 'Downtown Station', 'City Center',
            'Shopping Mall', 'Hospital', 'University Campus', 'Hotel Grand',
            'Train Station', 'Bus Terminal', 'Beach Resort', 'Business District',
        ];

        return [
            'user_id' => User::factory(),
            'driver_id' => null,
            'from_location' => fake()->randomElement($locations),
            'to_location' => [fake()->randomElement($locations), fake()->randomElement($locations)],
            'date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'time' => fake()->time('H:i'),
            'distance' => fake()->randomFloat(2, 5, 150),
            'going_distance' => fake()->randomFloat(2, 5, 150),
            'return_distance' => fake()->randomFloat(2, 0, 150),
            'passengers' => fake()->numberBetween(1, 8),
            'extra_large_bags' => fake()->boolean(30),
            'final_price' => fake()->randomFloat(2, 25, 500),
            'status' => fake()->randomElement(BookingStatus::cases()),
            'offer_id' => null,
            'notes' => fake()->optional(0.4)->sentence(),
            'is_completed' => fake()->boolean(70),
        ];
    }

    public function withDriver(): static
    {
        return $this->state(fn (array $attributes): array => [
            'driver_id' => Driver::factory(),
        ]);
    }

    public function withOffer(): static
    {
        return $this->state(fn (array $attributes): array => [
            'offer_id' => Offer::factory(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => BookingStatus::Completed,
            'is_completed' => true,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => BookingStatus::Pending,
            'is_completed' => false,
        ]);
    }
}
