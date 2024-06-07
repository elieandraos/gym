<?php

namespace App\Models;

use App\Enums\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;
use Laravel\Jetstream\HasProfilePhoto;

class User extends Authenticatable
{
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'registration_date',
        'in_house',
        'gender',
        'weight',
        'height',
        'birthdate',
        'blood_type',
        'phone_number',
        'instagram_handle',
        'address',
        'emergency_contact',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
        'age',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'registration_date' => 'date',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    public function memberBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'member_id');
    }

    public function trainerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainer_id');
    }

    public function scopeMembers(Builder $query): Builder
    {
        return $query->where('role', '=', Role::Member->value);
    }

    public function scopeTrainers(Builder $query): Builder
    {
        return $query->where('role', '=', Role::Trainer->value);
    }

    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->when($role === Role::Member->value, function ($query) {
            return $query->members();
        }, function ($query) use ($role) {
            return $role === Role::Trainer->value ? $query->trainers() : $query;
        });
    }

    public function getAgeAttribute(): ?string
    {
        $birthdate = Carbon::parse($this->birthdate);

        return $birthdate->diff(Carbon::now())->format('%y');
    }

    public function getSinceAttribute(): string
    {
        return Carbon::parse($this->registration_date)->format('M j, Y');
    }

    public function loadActiveBookingsWithSlots(): User
    {
        $bookingsRelation = match ($this->role) {
            Role::Member->value => 'memberBookings',
            Role::Trainer->value => 'trainerBookings',
            default => throw new InvalidArgumentException('Invalid role provided.'),
        };

        $memberOrTrainerRelation = match ($this->role) {
            Role::Member->value => 'trainer',
            Role::Trainer->value => 'member',
            default => throw new InvalidArgumentException('Invalid role provided.'),
        };

        return $this->load([
            $bookingsRelation => function ($query) use ($memberOrTrainerRelation) {
                $query->active()->with([
                    $memberOrTrainerRelation,
                    'bookingSlots' => function ($query) {
                        $query->orderBy('start_time');
                    },
                ]);
            },
        ]);
    }
}
