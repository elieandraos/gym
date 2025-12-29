<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
