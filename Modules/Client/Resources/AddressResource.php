<?php

namespace Modules\Client\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'details' => $this->details,
            'is_default' => $this->is_default,
            'city' => [
                'id' => $this->city->id ?? null,
                'name' => $this->city->name ?? null,
            ],
            'state' => [
                'id' => $this->state->id ?? null,
                'name' => $this->state->name ?? null,
            ]
        ];
    }
}
