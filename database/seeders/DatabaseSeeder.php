<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\AdditionalPrice;
use App\Models\BasePrice;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Driver;
use App\Models\Feedback;
use App\Models\Offer;
use App\Models\TimeRange;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@goo-taxi.com',
            'password' => Hash::make('secret'),
        ]);

         // Create base pricing
         BasePrice::factory()->create([
            'price_per_km' => 2.50,
            'van_price_percentage' => 20.00,
        ]);

        // Create contacts
        Contact::factory()->count(1)->create();


        // // Create regular users
        // $users = User::factory()->count(25)->create();

        // // Create drivers
        // $drivers = Driver::factory()->count(15)->create();



        // // Create additional prices for different zones
        // AdditionalPrice::factory()->count(8)->create();

        // // Create time ranges for peak hours
        // TimeRange::factory()->weekend()->create([
        //     'from_time' => '18:00',
        //     'to_time' => '23:59',
        //     'price_percentage' => 25.00,
        // ]);

        // TimeRange::factory()->weekday()->create([
        //     'from_time' => '07:00',
        //     'to_time' => '09:00',
        //     'price_percentage' => 15.00,
        // ]);

        // TimeRange::factory()->weekday()->create([
        //     'from_time' => '17:00',
        //     'to_time' => '19:00',
        //     'price_percentage' => 20.00,
        // ]);

        // // Create active offers
        // $activeOffers = collect([
        //     Offer::factory()->active()->create([
        //         'coupon_code' => 'WELCOME10',
        //         'discount_rate' => 10.00,
        //         'uses' => 100,
        //     ]),
        //     ...Offer::factory()->active()->count(4)->create(),
        //     ...Offer::factory()->active()->count(3)->create(),
        // ]);

        // // Create inactive offers
        // Offer::factory()->inactive()->count(2)->create();

        // // Create bookings with various statuses
        // // Completed bookings
        // Booking::factory()
        //     ->completed()
        //     ->count(30)
        //     ->create()
        //     ->each(function (Booking $booking) use ($drivers): void {
        //         $booking->update(['driver_id' => $drivers->random()->id]);
        //     });

        // // Pending bookings
        // Booking::factory()
        //     ->pending()
        //     ->count(10)
        //     ->create();

        // // Upcoming bookings
        // Booking::factory()
        //     ->state(['status' => BookingStatus::Upcoming])
        //     ->count(15)
        //     ->create()
        //     ->each(function (Booking $booking) use ($drivers): void {
        //         $booking->update(['driver_id' => $drivers->random()->id]);
        //     });

        // // Cancelled bookings
        // Booking::factory()
        //     ->state(['status' => BookingStatus::Cancelled])
        //     ->count(5)
        //     ->create();

        // // Rejected bookings
        // Booking::factory()
        //     ->state(['status' => BookingStatus::Rejected])
        //     ->count(3)
        //     ->create();

        // // Bookings with offers
        // Booking::factory()
        //     ->count(8)
        //     ->create()
        //     ->each(function (Booking $booking) use ($drivers, $activeOffers): void {
        //         $booking->update([
        //             'driver_id' => $drivers->random()->id,
        //             'offer_id' => $activeOffers->random()->id,
        //         ]);
        //     });


        // // Create feedback
        // Feedback::factory()->positive()->count(20)->create();
        // Feedback::factory()->negative()->count(5)->create();
        // Feedback::factory()->count(8)->create();
    }
}
