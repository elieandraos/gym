<?php

use App\Models\BookingSlot;
use App\Models\Workout;
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
        Schema::create('booking_slot_workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BookingSlot::class);
            $table->foreignIdFor(Workout::class);
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_slot_workouts');
    }
};
