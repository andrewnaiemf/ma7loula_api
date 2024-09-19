<?php
namespace Modules\Client\Requests\Address;

use Modules\Core\Requests\PublicRequest;

class CreateAddressRequest extends PublicRequest{
    public function rules(): array{
        return [
            'name' => ["required", "string"],
            "city_id" => ['required', 'exists:cities,id'],
            "state_id" => ['required', 'exists:states,id'],
            "lat" => ['nullable', 'string'],
            "lon" => ['nullable', 'string'],
            "details"  => ['nullable', 'string'],
            "is_default" => ['boolean', 'required']
        ];
    }
}