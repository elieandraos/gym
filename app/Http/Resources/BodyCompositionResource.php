<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\BodyComposition
 */
class BodyCompositionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'taken_at' => $this->taken_at,
            'taken_at_formatted' => $this->taken_at->format('D M jS, Y'),
            'photo_url' => asset('storage/'.$this->photo_path),
            'photo_path' => $this->photo_path,
        ];
    }
}
