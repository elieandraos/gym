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

    public function bookingSlotCircuitWorkouts(): HasMany
    {
        return $this->hasMany(BookingSlotCircuitWorkout::class);
    }
}
