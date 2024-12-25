<?php

namespace Modules\Emergency\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'order_id' => (int) $this->order_id,
            'balance' => (float) $this->balance,
            'date' => $this->created_at->format('Y-m-d H:i')
        ];
    }
}
