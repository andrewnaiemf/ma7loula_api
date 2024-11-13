<?php

namespace Modules\Client\Resources\WinchOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Modules\Core\Resources\SimpleResource;

class WinchOrderOfferResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;
        return [
            'id' => $this->id,
            'name' => $user->name,
            'phione' => $user->phone,
            'car_plate_number' => $this->car_plate_number,
            'expires_at' => (Carbon::now()->addMinute())->format('Y-m-d H:i:s'),
            'vendor' => new SimpleResource($this->vendor)
        ];
    }
}
