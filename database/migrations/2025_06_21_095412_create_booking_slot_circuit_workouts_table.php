<?php

use App\Models\BookingSlotCircuit;
use App\Models\Workout;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_slot_circuit_workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BookingSlotCircuit::class, 'booking_slot_circuit_id')
                ->constrained('booking_slot_circuits')
                ->onDelete('cascade');
            $table->foreignIdFor(Workout::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slot_circuit_workouts');
    }
};
