<?php

namespace Modules\Client\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Resources\MediaResource;

class ProductWithAttributesResource extends JsonResource
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
            'attributes' => ProductAttributeResource::collection($this->attrs),
            'images' => MediaResource::collection($this->allMedia),
            'brand' => new ProductBrandResource($this->brand),
            'category' => new ProductBrandResource($this->category),
            'vendor' => new ProductBrandResource($this->vendor),
        ];
    }
}
