<?php

namespace Modules\Emergency\Requests;

use Modules\Core\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SendOfferRequest extends Request
{

    public function authorize(): bool
    {
        $key = 'emergency_request_';
        return Auth::user() && Auth::user()->worker /* && Cache::has($key . Auth::user()->worker->id) */;
    }


    public function rules(): array
    {
        return
            [
                'order_id' => [
                    'required',
                    Rule::exists('order_emergencies', 'order_id')->whereNull('worker_id')->whereNull('deleted_at')
                ]
            ];
    }
}
