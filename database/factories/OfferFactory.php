<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OfferStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
final class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+3 months');

        return [
            'coupon_code' => mb_strtoupper(fake()->unique()->bothify('??##')),
            'discount_rate' => fake()->randomFloat(2, 5, 50),
            'number_of_times_used' => fake()->numberBetween(0, 100),
            'uses' => fake()->numberBetween(1, 1000),
            'status' => fake()->randomElement(OfferStatus::cases()),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }

    public function active(): static
    {
        return $this->state(function (array $attributes): array {
            $startDate = fake()->dateTimeBetween('-1 week', 'now');
            $endDate = fake()->dateTimeBetween('now', '+2 months');

            return [
                'status' => OfferStatus::Active,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => OfferStatus::Inactive,
        ]);
    }
}
