<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Scope as AsScope;

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

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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

    #[AsScope]
    public function active(Builder $query): Builder
    {
        return $query->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->orderBy('start_date', 'ASC');
    }

    #[AsScope]
    public function history(Builder $query): Builder
    {
        return $query->where('end_date', '<', Carbon::today())
            ->orderBy('start_date', 'DESC');
    }

    #[AsScope]
    public function scheduled(Builder $query): Builder
    {
        return $query->where('start_date', '>', Carbon::today())
            ->orderBy('start_date', 'ASC');
    }
}
