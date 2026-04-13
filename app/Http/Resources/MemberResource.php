<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => explode(' ', $this->name)[0],
            'email' => $this->email,
            'registration_date' => $this->registration_date,
            'since' => $this->since,
            'profile_photo_url' => $this->profile_photo_url,
            'profile_photo_path' => $this->profile_photo_path,
            'lead_source' => $this->lead_source?->value,
            'gender' => $this->gender,
            'weight' => $this->weight,
            'height' => $this->height,
            'age' => $this->age,
            'birthdate' => $this->birthdate,
            'birthdate_formatted' => Carbon::parse($this->birthdate)->format('M j, Y'),
            'blood_type' => $this->blood_type,
            'phone_number' => $this->phone_number,
            'instagram_handle' => $this->instagram_handle,
            'instagram_url' => 'https://www.instagram.com/'.$this->instagram_handle,
            'address' => $this->address,
            'emergency_contact' => $this->emergency_contact,
            'color' => $this->color,
            'role' => $this->role->value,
            'last_body_composition' => $this->whenLoaded('lastBodyComposition',
                fn () => new BodyCompositionResource($this->lastBodyComposition)
            ),
            // Minimal active booking reference for header (no full BookingResource to avoid circular refs)
            'active_booking' => $this->whenLoaded('memberActiveBooking', fn () => $this->memberActiveBooking ? [
                'id' => $this->memberActiveBooking->id,
                'is_frozen' => $this->memberActiveBooking->is_frozen,
                'is_paid' => $this->memberActiveBooking->is_paid,
                'amount' => $this->memberActiveBooking->amount,
            ] : null),
        ];
    }
}
