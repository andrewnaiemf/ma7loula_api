<?php

namespace Modules\Core\Requests\Auth;


class RegisterRequest extends ValidateOTPRequest
{
    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'min:3'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], parent::rules());
    }
}
