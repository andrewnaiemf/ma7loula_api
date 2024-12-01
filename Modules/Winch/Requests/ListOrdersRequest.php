<?php

namespace Modules\Winch\Requests;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;
use Illuminate\Validation\Rule;


class ListOrdersRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->worker;
    }

    public function rules(): array
    {
        $rules =  [];

        return $rules;
    }
}
