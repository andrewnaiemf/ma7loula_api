<?php
 
 namespace Modules\Client\Resources\Auth;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Interfaces\Auth\UserResource;

class ClientAuthResource extends JsonResource implements UserResource
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
            'default_address' => new AddressResource($this->defaultAddress)
        ];
    }
}