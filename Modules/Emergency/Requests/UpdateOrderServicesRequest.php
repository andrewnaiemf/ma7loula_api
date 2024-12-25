<?php

namespace Modules\Emergency\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\Request;


class UpdateOrderServicesRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->worker;
    }

    public function rules(): array
    {
        $rules =  [
            'order_id' => ['required', Rule::exists('order_vendors')->where('worker_id', Auth::user()->worker->id)],
            'services' => ['required', 'array'],
            'services.*.id' => ['required', 'exists:services,id'],
        ];

        return $rules;
    }
}
