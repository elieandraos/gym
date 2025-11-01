<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\Role;
use App\Traits\HasSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope as AsScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\HasProfilePhoto;

/**
 * @property-read int                              id
 * @property \Illuminate\Support\Carbon|mixed|null $birthdate
 * @property \Illuminate\Support\Carbon|mixed|null $registration_date
 * @property-read mixed                            $memberCompletedBookings
 *
 * @mixin HasSettings
 */
class User extends Authenticatable
{
    use HasFactory;
    use HasProfilePhoto;
    use HasSettings;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'registration_date',
        'lead_source',
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
        'color',
        'settings',
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
        'lead_source' => LeadSource::class,
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'settings' => 'array',
    ];

    public function trainerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainer_id');
    }

    public function trainerActiveBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainer_id')
            ->active()
            ->orderBy('start_date')
            ->with([
                'member',
                'bookingSlots' => fn ($query) => $query->orderBy('start_time'),
            ]);
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

    public function memberCompletedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'member_id')
            ->history()
            ->orderBy('start_date', 'desc')
            ->with('trainer');
    }

    public function bodyCompositions(): HasMany
    {
        return $this->hasMany(BodyComposition::class);
    }

    public function lastBodyComposition(): HasOne
    {
        return $this->hasOne(BodyComposition::class)->latestOfMany('taken_at');
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

    /**
     * Update the user's profile photo with user-specific subfolder and unique filename.
     */
    public function updateProfilePhoto(UploadedFile $photo, $storagePath = 'profile-photos'): void
    {
        tap($this->profile_photo_path, function ($previous) use ($photo, $storagePath) {
            // Generate unique filename: timestamp_hash.extension
            $filename = time().'_'.substr(md5(uniqid()), 0, 8).'.'.$photo->getClientOriginalExtension();

            // Store in user-specific subfolder: profile-photos/{user_id}/filename
            $path = $photo->storeAs(
                "{$storagePath}/{$this->id}",
                $filename,
                ['disk' => $this->profilePhotoDisk()]
            );

            $this->forceFill([
                'profile_photo_path' => $path,
            ])->save();

            // Delete previous photo if exists
            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     */
    public function deleteProfilePhoto(): void
    {
        if (is_null($this->profile_photo_path)) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

        $this->forceFill([
            'profile_photo_path' => null,
        ])->save();
    }
}
