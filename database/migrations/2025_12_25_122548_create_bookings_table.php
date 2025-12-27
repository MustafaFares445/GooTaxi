<?php

declare(strict_types=1);

use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_location');
            $table->string('to_location');
            $table->date('date');
            $table->time('time');
            $table->decimal('distance', 10, 2);
            $table->decimal('going_distance', 10, 2);
            $table->decimal('return_distance', 10, 2);
            $table->integer('passengers');
            $table->boolean('extra_large_bags')->default(false);
            $table->decimal('final_price', 10, 2);
            $table->enum('status', array_values(BookingStatus::cases()))->default(BookingStatus::Pending->value);
            $table->foreignId('offer_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('notes')->nullable();
            $table->boolean('is_completed')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
