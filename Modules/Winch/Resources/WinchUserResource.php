<?php

namespace Modules\Winch\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Resources\SimpleResource;

class WinchUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $worker = $this->worker;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'auth_token' => $this->auth_token,
            'worker' => new WorkerResource($worker),
            'vendor' => new SimpleResource($worker->vendor),
        ];
    }
}
