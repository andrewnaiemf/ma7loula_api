<?php

namespace Modules\Vendor\Requests\BT\Vendor;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;
use Illuminate\Validation\Rule;


class ListOrdersRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->vendor;
    }

    public function rules(): array
    {
        $rules =  [];

        return $rules;
    }
}
