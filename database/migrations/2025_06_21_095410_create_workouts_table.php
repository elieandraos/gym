<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the "workouts" table with id, name, categories, optional image, and timestamps.
     *
     * The table contains an auto-incrementing primary key (`id`), a string `name`,
     * a JSON `categories` column, a nullable `image` string, and `created_at`/`updated_at` timestamps.
     */
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('categories');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
