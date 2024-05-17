<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'registration_date' => $this->registration_date,
            'since' => $this->since,
            'profile_photo_url' => $this->profile_photo_url,
            'in_house' => $this->in_house,
            'gender' => $this->gender,
            'weight' => $this->weight,
            'height' => $this->height,
            'age' => $this->age,
            'birthdate' => $this->birthdate,
            'blood_type' => $this->blood_type,
            'phone_number' => $this->phone_number,
            'instagram_handle' => $this->instagram_handle,
            'address' => $this->address,
            'emergency_contact' => $this->emergency_contact,
            'role' => $this->role,
            'member_bookings' => BookingResource::collection($this->whenLoaded('memberBookings')),
            'trainer_bookings' => BookingResource::collection($this->whenLoaded('trainerBookings')),
        ];
    }
}
