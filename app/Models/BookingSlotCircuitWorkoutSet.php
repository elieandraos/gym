<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fillable attributes
 *
 * @property int $booking_slot_circuit_workout_id
 * @property int|null $reps
 * @property string|null $weight_in_kg
 * @property int|null $duration_in_seconds
 *
 * Auto-generated
 * @property-read int $id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 *
 * Relationships
 * @property-read BookingSlotCircuitWorkout $circuitWorkout
 */
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
