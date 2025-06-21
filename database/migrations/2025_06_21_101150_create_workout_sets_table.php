<?php

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
        Schema::create('workout_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_workout_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('reps')->default(12)->nullable();
            $table->boolean('is_timed')->default(false);
            $table->boolean('is_weighted')->default(true);
            $table->unsignedInteger('weight_kg')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_sets');
    }
};
