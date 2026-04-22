<?php

namespace Modules\Core\Requests\Auth;


class RegisterRequest extends ValidateOTPRequest
{
    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'min:3'],
            'password' => ['required', 'min:8', 'confirmed'],
            'fcm_token' => ['nullable', 'string', 'max:4096'],
            'device_id' => ['nullable', 'string', 'max:191'],
            'platform' => ['nullable', 'string', 'max:32'],
        ], parent::rules());
    }
}
