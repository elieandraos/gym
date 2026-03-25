<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fillable attributes
 *
 * @property int $booking_slot_circuit_id
 * @property int $workout_id
 * @property string|null $notes
 *
 * Auto-generated
 * @property-read int $id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 *
 * Relationships
 * @property-read BookingSlotCircuit $circuit
 * @property-read Workout $workout
 * @property-read Collection<BookingSlotCircuitWorkoutSet> $sets
 */
class BookingSlotCircuitWorkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_slot_circuit_id',
        'workout_id',
        'notes',
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
