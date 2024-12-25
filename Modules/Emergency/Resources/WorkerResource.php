<?php

namespace Modules\Emergency\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Resources\SimpleResource;

class WorkerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $worker = $this->worker;

        return [
            'id' => $this->id,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'car_plate_number' => $this->car_plate_number,
        ];
    }
}
