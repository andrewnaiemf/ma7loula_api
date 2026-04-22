<?php
namespace Modules\Vendor\Resources\BT\Vendor;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Client\Resources\OrderProductResource;
use Modules\Client\Resources\UserCarResource;

class OrderResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'status'            => $this->status,
            'reason'            => $this->reason,
            'created_at'     => $this->created_at?->format('Y-m-d h:i A'),
            'delivery_time'     => $this->delivery_time?->format('Y-m-d h:i A'),
            'payment_method'    => (float) $this->payment_method,
            'products_price'    => (float) $this->products_price,
            'services_price'    => (float) $this->services_price,
            'tax_price'         => (float) $this->tax_price,
            'delivery_price'    => (float) $this->delivery_price,
            'total'             => (float) $this->total,
            'created_at'        => $this->created_at?->format('Y-m-d h:i A'),
            'userCar'           => new UserCarResource($this->order->user_car),
            'products'          => OrderProductResource::collection($this->order->products)
        ];
    }
}
