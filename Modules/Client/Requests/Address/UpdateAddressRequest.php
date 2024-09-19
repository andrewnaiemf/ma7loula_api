<?php

namespace Modules\Client\Requests\Address;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends CreateAddressRequest
{
    public function rules(): array
    {
        return array_merge(
            [
                'id' => [
                    'required',
                    Rule::exists('addresses', 'id')->where('user_id', Auth::user()->id)->whereNull('deleted_at')
                ]
            ],
            parent::rules()
        );
    }
}
