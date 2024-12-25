<?php

namespace Modules\Client\Resources\EmergencyOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmergencyOrderServiceResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => (int) $this->id,
            'name'  => $this->name,
            'price' => (float) $this->price
        ];
    }
}
