<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 */
class BookingSlotCircuitWorkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_slot_circuit_id',
        'workout_id',
    ];

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(BookingSlotCircuit::class, 'booking_slot_circuit_id');
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(BookingSlotCircuitWorkoutSet::class);
    }
}
