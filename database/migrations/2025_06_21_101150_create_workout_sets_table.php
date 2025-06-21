<?php

use App\Models\BookingSlotWorkout;
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
        Schema::create('booking_slot_workout_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BookingSlotWorkout::class);
            $table->unsignedInteger('reps')->default(12)->nullable();
            $table->boolean('is_timed')->default(false);
            $table->boolean('is_weighted')->default(true);
            $table->decimal('weight_in_kg', 5, 2)->nullable();
            $table->unsignedInteger('duration_in_seconds')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_slot_workout_sets');
    }
};
