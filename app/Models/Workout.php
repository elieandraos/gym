<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fillable attributes
 *
 * @property string $name
 * @property array $categories
 * @property string|null $image
 *
 * Auto-generated
 * @property-read int $id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 *
 * Relationships
 * @property-read Collection<BookingSlotCircuitWorkout> $bookingSlotCircuitWorkouts
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
