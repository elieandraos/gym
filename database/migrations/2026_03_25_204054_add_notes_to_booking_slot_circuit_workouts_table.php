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
        Schema::table('booking_slot_circuit_workouts', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('workout_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_slot_circuit_workouts', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
