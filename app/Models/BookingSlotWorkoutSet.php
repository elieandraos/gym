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
        'duration_in_seconds',
    ];

    protected $casts = [
        'weight_in_kg'       => 'decimal:2',
        'reps'               => 'integer',
        'is_timed'           => 'boolean',
        'is_weighted'        => 'boolean',
        'duration_in_seconds'=> 'integer',
    ];


    public function bookingSlotWorkout(): BelongsTo
    {
        return $this->belongsTo(BookingSlotWorkout::class);
    }
}
