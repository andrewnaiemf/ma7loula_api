<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class SendOTPRequest extends PublicRequest
{
    public function rules(): array
    {
        $rules = [
            'purpose' => ['required', 'string', 'in:register,reset-password,update-phone'],
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015']
        ];

        switch ($this->input('purpose')) {
            case 'register':
            case 'update-phone':
                $rules['phone'][] = Rule::unique('users', 'phone')->whereNull('deleted_at');
                break;
            case 'reset-password':
                $rules['phone'][] = Rule::exists('users', 'phone')->whereNull('deleted_at');
                break;
        }

        return $rules;
    }
}
