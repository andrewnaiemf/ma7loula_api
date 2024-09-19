<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class UpdateProfileRequest extends PublicRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')->whereNot('id', Auth::user()->id)],
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::unique('users', 'phone')->whereNull('deleted_at')->whereNot('id', Auth::user()->id)],
        ];
    }
}
