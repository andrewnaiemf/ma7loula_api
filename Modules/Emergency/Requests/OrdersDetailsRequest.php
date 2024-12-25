<?php

namespace Modules\Emergency\Requests;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;
use Illuminate\Validation\Rule;


class OrdersDetailsRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->worker;
    }

    public function rules(): array
    {
        $rules =  [
            'id' => ['required', Rule::exists('order_vendors', 'order_id')->whereNull('deleted_at')->where('worker_id', Auth::user()->worker->id)]
        ];

        return $rules;
    }
}
