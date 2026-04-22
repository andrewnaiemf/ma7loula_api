<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class LoginRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015'],
            'password' => ['required', 'min:8'],
            'fcm_token' => ['nullable', 'string', 'max:4096'],
            'device_id' => ['nullable', 'string', 'max:191'],
            'platform' => ['nullable', 'string', 'max:32'],
        ];
    }
}