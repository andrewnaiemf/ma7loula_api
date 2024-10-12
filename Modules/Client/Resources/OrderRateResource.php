<?php

namespace Modules\Client\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRateResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'products'  => $this->products,
            'services'  => $this->services,
            'worker'  => $this->worker,
            'comment'  => $this->comment,
        ];
    }
}
