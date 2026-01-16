<?php

namespace App\Models;

use App\Enums\Status;
use App\Models\Traits\UpdatesBookingEndDate;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Scope as AsScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fillable attributes
 *
 * @property int $nb_sessions
 * @property int $member_id
 * @property int $trainer_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property bool $is_paid
 * @property string $amount
 * @property array $schedule_days
 * @property bool $is_frozen
 * @property Carbon|null $frozen_at
 *
 * Auto-generated
 * @property-read int $id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 *
 * Relationships
 * @property-read User $member
 * @property-read User $trainer
 * @property-read Collection<BookingSlot> $bookingSlots
 *
 * Scopes
 *
 * @method static Builder|Booking active()
 * @method static Builder|Booking history()
 * @method static Builder|Booking scheduled()
 * @method static Builder|Booking between(DateTimeInterface $start, DateTimeInterface $end)
 * @method static Builder|Booking forCalendar(Carbon $start, Carbon $end)
 *
 * @mixin UpdatesBookingEndDate
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
        'amount',
        'schedule_days',
        'is_frozen',
        'frozen_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'nb_sessions' => 'integer',
        'is_paid' => 'boolean',
        'amount' => 'decimal:2',
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

    /** @noinspection PhpUndefinedMethodInspection - Scope calling another scope, PhpStorm can't recognize it */
    #[AsScope]
    public function forCalendar(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->with([
            'bookingSlots' => fn ($q) => $q->between($start, $end)
                ->whereNot('status', Status::Cancelled)
                ->whereNot('status', Status::Frozen),
            'member',
            'trainer',
        ])->between($start, $end);
    }
}
