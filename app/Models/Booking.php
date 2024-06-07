<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'nb_sessions',
        'member_id',
        'trainer_id',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function bookingSlots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today());
    }

    public function scopeHistory(Builder $query): Builder
    {
        return $query->where('end_date', '<', Carbon::today())
            ->orderBy('start_date', 'ASC');
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('start_date', '>', Carbon::now());
    }
}
