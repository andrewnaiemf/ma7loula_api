<?php

namespace Modules\Emergency\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'amount' => (float) $this->amount,
            'method' => $this->method,
            'status' => $this->status,
            'date' => $this->created_at->format('Y-m-d H:i')
        ];
    }
}
