<?php

namespace Modules\Vendor\Resources\BT\Vendor;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorUserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'auth_token' => $this->auth_token,
            'vendor' => new VendorResource($this->vendor)
        ];
    }
}
