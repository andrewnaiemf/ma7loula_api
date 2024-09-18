<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ResetPasswordRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::exists('users', 'phone')->whereNull('deleted_at')],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }
}