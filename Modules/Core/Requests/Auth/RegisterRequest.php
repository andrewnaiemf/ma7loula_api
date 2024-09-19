<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;

class RegisterRequest extends ValidateOTPRequest
{
    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::unique('users', 'phone')->whereNull('deleted_at')],
            'password' => ['required', 'min:8', 'confirmed'],
        ], parent::rules());
    }
}
