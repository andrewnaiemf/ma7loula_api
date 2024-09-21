<?php
 
 namespace Modules\Client\Resources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Resources\CarResource;

class ClientAuthResource extends JsonResource
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
            'default_address' => new AddressResource($this->defaultAddress),
            'default_car' => new CarResource($this->defaultCar)
        ];
    }
}