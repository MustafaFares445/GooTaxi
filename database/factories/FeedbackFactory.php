<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        return [
            'fullName' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'message' => $this->faker->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => $this->faker->randomElement([
                'Excellent service! Very professional driver and comfortable ride.',
                'Great experience! The driver was punctual and friendly.',
                'Amazing service! Highly recommend Goo-Taxi to everyone.',
                'Outstanding service! Clean vehicle and safe driving.',
                'Perfect ride! The driver was courteous and the journey was smooth.',
                'Wonderful experience! Will definitely use this service again.',
                'Top-notch service! Professional and reliable.',
                'Fantastic driver! Made my journey very pleasant.',
                'Excellent customer service! Very satisfied with the ride.',
                'Great value for money! Highly satisfied with the service.',
            ]),
        ]);
    }

    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => $this->faker->randomElement([
                'The driver was late and the vehicle was not clean.',
                'Poor service. The driver was rude and unprofessional.',
                'Disappointed with the service. The ride was uncomfortable.',
                'Not satisfied with the experience. The driver took a longer route.',
                'The vehicle had issues and the driver was not helpful.',
                'Poor communication. The driver did not follow instructions.',
                'Unprofessional service. Would not recommend.',
                'The ride was delayed and the driver was not apologetic.',
            ]),
        ]);
    }
}
