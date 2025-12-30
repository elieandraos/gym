<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int       $id
 * @property-read string    $name
 * @property-read array     $categories
 */
class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'categories',
        'image',
    ];

    protected $casts = [
        'categories' => 'array',
    ];

    /**
     * Get the one-to-many relationship to booking slot circuit workouts for this workout.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The relation linking this workout to its BookingSlotCircuitWorkout models.
     */
    public function bookingSlotCircuitWorkouts(): HasMany
    {
        return $this->hasMany(BookingSlotCircuitWorkout::class);
    }
}