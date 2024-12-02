<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class SendOTPRequest extends PublicRequest
{
    private $header_to_role_id = [
        'client' => 2,
        'vendor_cp' => 3,
        'vendor_bt' => 4,
        'winch_driver' => 5,
        'worker_bt' => 6,
        'worker_sos' => 7
    ];

    public function rules(): array
    {

        $rules = [
            'purpose' => ['required', 'string', 'in:register,reset-password,update-phone'],
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015']
        ];

        switch ($this->input('purpose')) {
            case 'register':
            case 'update-phone':
                $rules['phone'][] = Rule::unique('users', 'phone')->where('role_id', $this->header_to_role_id[$this->header('App')])->whereNull('deleted_at');
                break;
            case 'reset-password':
                $rules['phone'][] = Rule::exists('users', 'phone')->where('role_id', $this->header_to_role_id[$this->header('App')])->whereNull('deleted_at');
                break;
        }

        return $rules;
    }
}
