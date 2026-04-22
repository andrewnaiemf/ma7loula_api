<?php

namespace Modules\Client\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\Auth\ValidateOTPRequest;

class RegisterRequest extends ValidateOTPRequest
{
    public function rules(): array
    {
        return array_merge([
            'email' => ['required', 'email', Rule::unique('users', 'email')->where('role_id', 2)->whereNull('deleted_at')],
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::unique('users', 'phone')->where('role_id', 2)->whereNull('deleted_at')],
            'password' => ['required', 'min:8', 'confirmed'],
        ], parent::rules());
    }
}
