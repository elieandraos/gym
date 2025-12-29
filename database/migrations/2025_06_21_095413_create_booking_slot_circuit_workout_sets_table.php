<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_slot_circuit_workout_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_slot_circuit_workout_id');
            $table->unsignedInteger('reps')->default(12);
            $table->decimal('weight_in_kg', 5)->nullable();
            $table->unsignedInteger('duration_in_seconds')->nullable();
            $table->timestamps();

            // Add FK with custom short name to avoid MySQL 64-char limit
            $table->foreign('booking_slot_circuit_workout_id', 'bscws_bscw_id_foreign')
                ->references('id')
                ->on('booking_slot_circuit_workouts')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slot_circuit_workout_sets');
    }
};
