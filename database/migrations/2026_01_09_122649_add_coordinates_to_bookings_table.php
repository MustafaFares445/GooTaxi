<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->decimal('starting_lat', 10, 8)->nullable()->after('return_distance');
            $table->decimal('starting_lng', 11, 8)->nullable()->after('starting_lat');
            $table->decimal('ending_lat', 10, 8)->nullable()->after('starting_lng');
            $table->decimal('ending_lng', 11, 8)->nullable()->after('ending_lat');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropColumn(['starting_lat', 'starting_lng', 'ending_lat', 'ending_lng']);
        });
    }
};
