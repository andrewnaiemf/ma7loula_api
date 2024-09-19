<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;

class UpdatePhoneRequest extends ValidateOTPRequest
{
    public function rules(): array{
        return array_merge([
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::unique('users', 'phone')->whereNull('deleted_at')],
        ], parent::rules());
    }
}