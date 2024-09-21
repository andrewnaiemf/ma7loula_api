<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class VerifyOTPRequest extends ValidateOTPRequest
{
    public function rules(): array
    {
        return array_merge([
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015'],
        ], parent::rules());
    }
}
