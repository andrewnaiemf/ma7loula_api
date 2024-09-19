<?php

namespace Modules\Client\Requests\Address;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class DeleteAddressRequest extends PublicRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                Rule::exists('addresses', 'id')->where('user_id', Auth::user()->id)->whereNull('deleted_at')
            ]
        ];
    }
}
