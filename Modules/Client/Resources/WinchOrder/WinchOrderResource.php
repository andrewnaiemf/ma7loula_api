<?php

namespace Modules\Client\Resources\WinchOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Client\Resources\ClientResource;
use Modules\Client\Resources\OrderRateResource;
use Modules\Client\Resources\OrderStatusesResource;
use Modules\Client\Resources\UserCarResource;
use Modules\Core\Resources\SimpleResource;

class WinchOrderResource extends JsonResource
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
            'status'                => $this->status,
            'reason'            => $this->reason,
            'payment_method'        => $this->payment_method,
            'created_at'     => $this->created_at?->format('Y-m-d h:i A'),
            'delivery_time'     => $this->delivery_time?->format('Y-m-d h:i A'),
            'services_price'        => (float) $this->services_price,
            'tax_price'             => (float) $this->tax_price,
            'total'                 => (float) $this->total,
            'user'                  => new ClientResource($this->user),
            'userCar'               => new UserCarResource($this->user_car),
            'statueses'             => OrderStatusesResource::collection($this->statuses),
            'rate'                  => new OrderRateResource($this->rate),
            'from_lat'              => (float) $this->winch_order->from_lat,
            'from_lon'              => $this->winch_order->from_lon,
            'to_lat'                => $this->winch_order->to_lat,
            'to_lon'                => $this->winch_order->to_lon,
            'from_text'             => $this->winch_order->from_text,
            'to_text'               => $this->winch_order->to_text,
            'distance_in_meters'    => $this->winch_order->distance_in_meters,
            'duration_in_minutes'   => $this->winch_order->duration_in_minutes,
            'vendor'                => new SimpleResource($this->winch_order->vendor),
            'worker'               => new WinchOrderWorkerResource($this->winch_order->worker),
        ];
    }
}
