<?php

use App\Enums\Gender;
use App\Enums\Status;
use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $statuses = collect(Status::cases())->map(fn ($case) => $case->value)->toArray();

        Schema::create('booking_slots', function (Blueprint $table) use($statuses) {
            $table->id();
            $table->foreignIdFor(Booking::class);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', $statuses);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
};
