<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_ranges', static function (Blueprint $table): void {
            $table->id();
            $table->json('days')->nullable();
            $table->time('from_time');
            $table->time('to_time');
            $table->decimal('price_percentage', 5, 2);
            $table->decimal('start_price', 10, 2)->nullable();
            $table->decimal('price_of_going_per_km', 10, 2)->nullable();
            $table->decimal('return_price_per_km', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_ranges');
    }
};
