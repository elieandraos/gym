<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('nb_sessions');
            $table->foreignId('member_id')->constrained('users');
            $table->foreignId('trainer_id')->constrained('users');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_frozen')->default(false);
            $table->datetime('frozen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
