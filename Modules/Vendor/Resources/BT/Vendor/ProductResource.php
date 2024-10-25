<?php

namespace Modules\Vendor\Resources\BT\Vendor;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Client\Resources\ProductAttributeResource;
use Modules\Core\Resources\MediaResource;
use Modules\Core\Resources\SimpleResource;

class ProductResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'price_before_discount' => (float)  $this->price_before_discount,
            'stock' => (int) $this->stock,
            'status' => $this->status,
            'thumbnail' => new MediaResource($this->thumbnail),
            'images' => MediaResource::collection($this->allMedia),
            'brand' => new SimpleResource($this->brand),
            'attributes' => ProductAttributeResource::collection($this->attrs),
        ];
    }
}
