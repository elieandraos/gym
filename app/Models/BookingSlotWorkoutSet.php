<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSlotWorkoutSet extends Model
{
    protected $fillable = [
        'booking_slot_workout_id',
        'reps',
        'is_timed',
        'is_weighted',
        'weight_in_kg',
        'duration_in_seconds'
    ];

    public function bookingSlotWorkout(): BelongsTo
    {
        return $this->belongsTo(BookingSlotWorkout::class);
    }
}
