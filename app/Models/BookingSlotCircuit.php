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
 * @property int $booking_slot_id
 * @property string|null $name
 *
 * Auto-generated
 * @property-read int $id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 *
 * Relationships
 * @property-read BookingSlot $bookingSlot
 * @property-read Collection<BookingSlotCircuitWorkout> $circuitWorkouts
 */
class BookingSlotCircuit extends Model
{
    use HasFactory;

    protected $fillable = ['booking_slot_id', 'name'];

    public function bookingSlot(): BelongsTo
    {
        return $this->belongsTo(BookingSlot::class);
    }

    public function circuitWorkouts(): HasMany
    {
        return $this->hasMany(BookingSlotCircuitWorkout::class);
    }
}
