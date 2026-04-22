<?php

namespace Modules\Client\Resources\EmergencyOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Client\Resources\OrderRateResource;
use Modules\Client\Resources\OrderStatusesResource;
use Modules\Client\Resources\UserCarResource;
use Modules\Client\Resources\ClientResource;
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
            'created_at'     => $this->created_at?->format('Y-m-d h:i A'),
            'delivery_time'     => $this->delivery_time?->format('Y-m-d h:i A'),
            'description'           => $this->emergency_order?->description,
            'lat'                   => $this->emergency_order?->lat,
            'lon'                   => $this->emergency_order?->lon,
            'location'              => $this->emergency_order?->location,
            'record'                => $this->emergency_order?->record ? url($this->emergency_order->record) : null,
            'status'                => $this->status,
            'reason'            => $this->reason,
            'payment_method'        => $this->payment_method,
            'services_price'        => (float) $this->services_price,
            'tax_price'             => (float) $this->tax_price,
            'total'                 => (float) $this->total,
            'user'               => new ClientResource($this->user),
            'userCar'               => new UserCarResource($this->user_car),
            'statueses'             => OrderStatusesResource::collection($this->statuses),
            'rate'                  => new OrderRateResource($this->rate),
            'vendor'                => new SimpleResource($this->emergency_order?->vendor),
            'worker'                => new WinchOrderWorkerResource($this->emergency_order?->worker),
            'services'              => EmergencyOrderServiceResource::collection($this->services)
        ];
    }
}
