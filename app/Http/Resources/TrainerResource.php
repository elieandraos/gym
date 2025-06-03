<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class TrainerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // If the relationship is loaded, use it; otherwise use an empty Collection.
        $bookings = $this->relationLoaded('trainerActiveBookings')
            ? $this->trainerActiveBookings
            : collect();

        // Extract unique member names:
        $memberNames = $bookings
            ->pluck('member.name')
            ->unique()
            ->values();

        $totalMembers    = $memberNames->count();
        $firstTwoNames   = $memberNames->take(2)->values();
        $additionalCount = max($totalMembers - $firstTwoNames->count(), 0);

        return [
            'id'                             => $this->id,
            'name'                           => $this->name,
            'first_name'                     => explode(' ', $this->name)[0],
            'email'                          => $this->email,
            'registration_date'              => $this->registration_date,
            'since'                          => $this->since,
            'profile_photo_url'              => $this->profile_photo_url,
            'in_house'                       => $this->in_house,
            'gender'                         => $this->gender,
            'weight'                         => $this->weight,
            'height'                         => $this->height,
            'age'                            => $this->age,
            'birthdate'                      => Carbon::parse($this->birthdate)->format('M j, Y'),
            'blood_type'                     => $this->blood_type,
            'phone_number'                   => $this->phone_number,
            'instagram_handle'               => $this->instagram_handle,
            'instagram_url'                  => 'https://www.instagram.com/'.$this->instagram_handle,
            'address'                        => $this->address,
            'emergency_contact'              => $this->emergency_contact,
            'role'                           => strtolower($this->role),

            // Always include the bookings themselves (if loaded)
            'active_bookings' => BookingResource::collection($bookings),

            // Conditionally merge only if the relation was eager-loaded
            $this->mergeWhen($this->relationLoaded('trainerActiveBookings'), [
                'active_members_count'            => $totalMembers,
                'active_member_names'             => $firstTwoNames,
                'active_members_additional_count' => $additionalCount,
                'active_member_full_names'        => $memberNames,
            ]),
        ];
    }
}
