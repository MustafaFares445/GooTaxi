<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_prices', static function (Blueprint $table): void {
            $table->id();
            $table->decimal('start_price', 10, 2);
            $table->decimal('price_of_going_per_km', 10, 2);
            $table->decimal('return_price_per_km', 10, 2);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_prices');
    }
};
