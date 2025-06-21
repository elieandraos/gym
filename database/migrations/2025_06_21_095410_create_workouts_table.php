<?php

use App\Enums\Category;
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
        $categories = collect(Category::cases())->map(fn ($case) => $case->value)->toArray();

        Schema::create('workouts', function (Blueprint $table) use ($categories) {
            $table->id();
            $table->string('name');
            $table->enum('category', $categories)->index();
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
