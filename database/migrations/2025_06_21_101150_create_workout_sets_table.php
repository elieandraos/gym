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
            $table->foreignIdFor(BookingSlotWorkout::class)->onDelete('cascade');
            $table->unsignedInteger('reps')->default(12);
            $table->decimal('weight_in_kg', 5)->nullable();
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
