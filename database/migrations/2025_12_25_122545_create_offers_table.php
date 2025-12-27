<?php

declare(strict_types=1);

use App\Enums\OfferStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table): void {
            $table->id();
            $table->string('coupon_code')->unique();
            $table->decimal('discount_rate', 5, 2);
            $table->integer('number_of_times_used');
            $table->integer('uses')->default(0);
            $table->enum('status', array_values(OfferStatus::cases()))->default(OfferStatus::Active->value);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
