<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
final class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'is_admin' => false,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_admin' => true,
            'email' => 'admin@goo-taxi.com',
        ]);
    }
}
