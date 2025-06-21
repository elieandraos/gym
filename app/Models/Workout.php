<?php

namespace App\Models;

use App\Enums\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workout extends Model
{
    protected $fillable = [
        'name',
        'category',
        'image'
    ];

    protected $casts = [
        'category' => Category::class,
    ];

    public function bookingSlotWorkouts(): HasMany
    {
        return $this->hasMany(BookingSlotWorkout::class);
    }
}
