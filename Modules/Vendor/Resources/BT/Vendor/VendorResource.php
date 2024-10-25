<?php

namespace Modules\Vendor\Resources\BT\Vendor;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'address' => $this->address,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'tax_no' => $this->tax_no,
            'company_licence_expire_date' => $this->company_licence_expire_date,
            'company_licence_no' => $this->company_licence_no,
            'id_image' => url('storage/bt-vendor/'.$this->id_image),
            'company_licence_image' => url('storage/bt-vendor/'.$this->company_licence_image),
        ];
    }
}
