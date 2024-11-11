<?php

namespace Modules\Client\Resources\WinchOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WinchOrderWorkerResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->user->name,
            'phone'  => $this->user->phone,
        ];
    }
}
