<?php

namespace Modules\Winch\Requests;

use Modules\Core\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class SendOfferRequest extends Request
{

    public function authorize(): bool
    {
        $key = 'winch_request_';
        return Auth::user() && Auth::user()->worker /* && Cache::has($key . Auth::user()->worker->id) */;
    }


    public function rules(): array
    {
        return
            [
                'order_id' => [
                    'required',
                    Rule::exists('order_winches', 'order_id')->whereNull('worker_id')->whereNull('deleted_at')
                ]
            ];
    }
}
