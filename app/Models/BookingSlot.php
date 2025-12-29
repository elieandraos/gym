<?php

namespace App\Models;

use App\Enums\Status;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Scope as AsScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => Status::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function circuits(): HasMany
    {
        return $this->hasMany(BookingSlotCircuit::class);
    }

    #[AsScope]
    public function between(Builder $query, DateTimeInterface $start, DateTimeInterface $end): Builder
    {
        $start = Carbon::instance($start);
        $end = Carbon::instance($end);

        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_time', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
                ->orWhereBetween('end_time', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
        });
    }
}
