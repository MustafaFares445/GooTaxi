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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->decimal('distance', 10, 2)->nullable();
            $table->decimal('going_distance', 10, 2)->nullable();
            $table->decimal('return_distance', 10, 2)->nullable();
            $table->integer('passengers')->default(1);
            $table->boolean('extra_large_bags')->default(false);
            $table->decimal('final_price', 10, 2)->nullable();
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
