<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;

class ResetPasswordRequest extends ValidateOTPRequest
{
    private $header_to_role_id = [
        'client' => 2,
        'vendor_cp' => 3,
        'vendor_bt' => 4,
        'winch_driver' => 5,
        'worker_bt' => 6,
        'worker_sos' => 7
    ];

    public function rules(): array{
        return array_merge([
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::exists('users', 'phone')->where('role_id', $this->header_to_role_id[$this->header('App')])->whereNull('deleted_at')],
            'password' => ['required', 'min:8', 'confirmed'],
        ], parent::rules());
    }
}