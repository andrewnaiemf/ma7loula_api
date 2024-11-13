<?php

namespace Modules\Client\Resources\EmergencyOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Client\Resources\OrderRateResource;
use Modules\Client\Resources\OrderStatusesResource;
use Modules\Client\Resources\UserCarResource;
use Modules\Client\Resources\WinchOrder\WinchOrderWorkerResource;
use Modules\Core\Resources\SimpleResource;

class EmergencyOrderResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'description'           => $this->emergency_order?->description,
            'record'                => $this->emergency_order?->record ? url($this->emergency_order->record) : null,
            'status'                => $this->status,
            'payment_method'        => $this->payment_method,
            'services_price'        => (float) $this->services_price,
            'tax_price'             => (float) $this->tax_price,
            'total'                 => (float) $this->total,
            'userCar'               => new UserCarResource($this->user_car),
            'statueses'             => OrderStatusesResource::collection($this->statuses),
            'rate'                  => new OrderRateResource($this->rate),
            'vendor'                => new SimpleResource($this->emergency_order?->vendor),
            'worker'               => new WinchOrderWorkerResource($this->emergency_order?->worker),
        ];
    }
}
