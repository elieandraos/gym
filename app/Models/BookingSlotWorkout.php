<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSlotWorkout extends Model
{
    protected $fillable = [
        'booking_slot_id',
        'workout_id',
        'image',
        'notes'
    ];

    public function bookingSlot(): BelongsTo
    {
        return $this->belongsTo(BookingSlot::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(BookingSlotWorkoutSet::class);
    }
}
