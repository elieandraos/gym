<?php

namespace App\Models;

use App\Enums\Status;
use App\Models\Traits\UpdatesBookingEndDate;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Scope as AsScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read bool $is_paid
 * @property array $schedule_days
 */
class Booking extends Model
{
    use HasFactory, UpdatesBookingEndDate;

    protected $fillable = [
        'nb_sessions',
        'member_id',
        'trainer_id',
        'start_date',
        'end_date',
        'is_paid',
        'schedule_days',
        'is_frozen',
        'frozen_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'nb_sessions' => 'integer',
        'is_paid' => 'boolean',
        'schedule_days' => 'array',
        'is_frozen' => 'boolean',
        'frozen_at' => 'datetime',
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

    #[AsScope]
    public function between(Builder $query, DateTimeInterface $start, DateTimeInterface $end): Builder
    {
        $start = Carbon::instance($start);
        $end = Carbon::instance($end);

        return $query->where(function ($q) use ($start, $end) {
            $q->where('start_date', '<=', $end->toDateString())
                ->where('end_date', '>=', $start->toDateString());
        });
    }

    #[AsScope]
    public function forCalendar(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->with([
            'bookingSlots' => fn ($q) => $q->between($start, $end)
                ->whereNot('status', Status::Cancelled)
                ->whereNot('status', Status::Frozen),
            'member:id,name',
            'trainer:id,name,color',
        ])->between($start, $end);
    }
}
