<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Helpers\AppToRole;

class ResetPasswordRequest extends ValidateOTPRequest
{
    public function rules(): array{
        return array_merge([
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::exists('users', 'phone')->where('role_id', AppToRole::getRoleId($this->header('App')))->whereNull('deleted_at')],
            'password' => ['required', 'min:8', 'confirmed'],
        ], parent::rules());
    }
}