<?php

namespace App\Http\Resources;

use App\Enums\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => explode(" ",$this->name)[0],
            'email' => $this->email,
            'registration_date' => $this->registration_date,
            'since' => $this->since,
            'profile_photo_url' => $this->profile_photo_url,
            'in_house' => $this->in_house,
            'gender' => $this->gender,
            'weight' => $this->weight,
            'height' => $this->height,
            'age' => $this->age,
            'birthdate' => Carbon::parse($this->birthdate)->format('M j, Y'),
            'blood_type' => $this->blood_type,
            'phone_number' => $this->phone_number,
            'instagram_handle' => $this->instagram_handle,
            'instagram_url' => 'https://www.instagram.com/'.$this->instagram_handle,
            'address' => $this->address,
            'emergency_contact' => $this->emergency_contact,
            'role' => $this->role,
            'bookings' => $this->getBookings(),
        ];
    }

    protected function getBookings(): AnonymousResourceCollection
    {
        return $this->role === Role::Member->value ?
            BookingResource::collection($this->whenLoaded('memberBookings')) :
            BookingResource::collection($this->whenLoaded('trainerBookings')) ;
    }
}
