<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSlotCircuitWorkoutSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_slot_circuit_workout_id',
        'reps',
        'weight_in_kg',
        'duration_in_seconds',
    ];

    protected $casts = [
        'weight_in_kg' => 'decimal:2',
        'reps' => 'integer',
        'duration_in_seconds' => 'integer',
    ];

    public function circuitWorkout(): BelongsTo
    {
        return $this->belongsTo(BookingSlotCircuitWorkout::class, 'booking_slot_circuit_workout_id');
    }
}
