<?php

namespace App\Models;

use App\Enums\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope as AsScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Jetstream\HasProfilePhoto;

/**
 * @property \Illuminate\Support\Carbon|mixed|null $birthdate
 * @property \Illuminate\Support\Carbon|mixed|null $registration_date
 */
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

    protected $casts = [
        'birthdate' => 'date',
        'registration_date' => 'date',
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    public function trainerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainer_id');
    }

    public function memberBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'member_id');
    }

    public function memberActiveBooking(): HasOne
    {
        return $this->hasOne(Booking::class, 'member_id')
            ->active()
            ->orderBy('start_date')
            ->with([
                'trainer',
                'bookingSlots' => fn ($query) => $query->orderBy('start_time'),
            ]);
    }

    public function memberScheduledBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'member_id')
            ->scheduled()
            ->orderBy('start_date')
            ->with([
                'trainer',
                'bookingSlots' => fn ($query) => $query->orderBy('start_time'),
            ]);
    }

    #[AsScope]
    public function members(Builder $query): Builder
    {
        return $query->where('role', '=', Role::Member->value);
    }

    #[AsScope]
    public function trainers(Builder $query): Builder
    {
        return $query->where('role', '=', Role::Trainer->value);
    }

    #[AsScope]
    public function byRole(Builder $query, string $role): Builder
    {
        return $query->when($role === Role::Member->value, function ($query) {
            return $query->members();
        }, function ($query) use ($role) {
            return $role === Role::Trainer->value ? $query->trainers() : $query;
        });
    }

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birthdate
                ? Carbon::parse($this->birthdate)->diff(Carbon::now())->format('%y')
                : null,
        );
    }


    public function since(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->registration_date)->format('M j, Y'),
        );
    }
}
